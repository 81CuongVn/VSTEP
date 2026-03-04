import { scoreToBand } from "../../src/common/scoring";
import type { DbTransaction } from "../../src/db/index";
import type {
  NewExamAnswer,
  NewExamSession,
  NewExamSubmission,
} from "../../src/db/schema/exams";
import { table } from "../../src/db/schema/index";
import type { SubmissionAnswer } from "../../src/db/types/answers";
import { logResult, logSection } from "../utils";
import type { SeededUsers } from "./01-users";
import type { SeededQuestions } from "./02-questions";
import type { SeededExams } from "./03-exams";
import type { SeededSubmissions } from "./05-submissions";

function findExam(
  exams: SeededExams["all"],
  level: string,
  type?: string,
): string {
  const exam = exams.find(
    (e) => e.level === level && (!type || e.type === type),
  );
  if (!exam)
    throw new Error(
      `No exam found for level: ${level}${type ? `, type: ${type}` : ""}`,
    );
  return exam.id;
}

function findQuestion(
  questions: SeededQuestions["all"],
  skill: string,
): string {
  const q = questions.find((q) => q.skill === skill);
  if (!q) throw new Error(`No question found for skill: ${skill}`);
  return q.id;
}

function objectiveAnswer(count: number): SubmissionAnswer {
  const options = ["A", "B", "C", "D"];
  const answers: Record<string, string> = {};
  for (let i = 1; i <= count; i++) {
    answers[String(i)] = options[i % options.length];
  }
  return { answers };
}

export interface SeededSessions {
  all: Array<{ id: string; status: string }>;
}

export async function seedExamSessions(
  db: DbTransaction,
  users: SeededUsers,
  exams: SeededExams,
  questions: SeededQuestions["all"],
  submissions: SeededSubmissions,
): Promise<SeededSessions> {
  logSection("Exam Sessions");

  const learner1 = users.learners[0].id;
  const learner2 = users.learners[1].id;
  const b2ExamId = findExam(exams.all, "B2");
  const b1ExamId = findExam(exams.all, "B1");
  const placementExamId = findExam(exams.all, "B1", "placement");

  const sessionData: NewExamSession[] = [
    // Session 1: Learner 1, B2 exam, completed
    {
      userId: learner1,
      examId: b2ExamId,
      status: "completed",
      listeningScore: 7.0,
      readingScore: 7.5,
      writingScore: 6.0,
      speakingScore: 5.5,
      overallScore: 6.5,
      overallBand: scoreToBand(6.5),
      startedAt: new Date("2026-01-10T08:00:00Z").toISOString(),
      completedAt: new Date("2026-01-10T10:30:00Z").toISOString(),
    },
    // Session 2: Learner 1, B1 exam, in_progress
    {
      userId: learner1,
      examId: b1ExamId,
      status: "in_progress",
      startedAt: new Date("2026-02-01T08:00:00Z").toISOString(),
    },
    // Session 3: Learner 2, B1 exam, completed
    {
      userId: learner2,
      examId: b1ExamId,
      status: "completed",
      listeningScore: 5.0,
      readingScore: 5.5,
      writingScore: 4.0,
      speakingScore: 4.0,
      overallScore: 4.5,
      overallBand: scoreToBand(4.5),
      startedAt: new Date("2026-01-20T08:00:00Z").toISOString(),
      completedAt: new Date("2026-01-20T10:30:00Z").toISOString(),
    },
    // Session 4: Learner 1, placement exam, completed
    {
      userId: learner1,
      examId: placementExamId,
      status: "completed",
      listeningScore: 5.0,
      readingScore: 6.0,
      writingScore: 5.5,
      speakingScore: 4.5,
      overallScore: 5.5,
      overallBand: scoreToBand(5.5),
      startedAt: new Date("2025-12-15T08:00:00Z").toISOString(),
      completedAt: new Date("2025-12-15T10:00:00Z").toISOString(),
    },
  ];

  const sessions = await db
    .insert(table.examSessions)
    .values(sessionData)
    .returning({
      id: table.examSessions.id,
      status: table.examSessions.status,
    });

  logResult("Exam sessions", sessions.length);

  // Exam answers — 2-3 per session
  const listeningQ = findQuestion(questions, "listening");
  const readingQ = findQuestion(questions, "reading");
  const writingQ = findQuestion(questions, "writing");

  const answerData: NewExamAnswer[] = [
    // Session 1 (completed B2): listening + reading + writing answers
    {
      sessionId: sessions[0].id,
      questionId: listeningQ,
      answer: objectiveAnswer(10),
      isCorrect: true,
    },
    {
      sessionId: sessions[0].id,
      questionId: readingQ,
      answer: objectiveAnswer(10),
      isCorrect: true,
    },
    {
      sessionId: sessions[0].id,
      questionId: writingQ,
      answer: {
        text: "Education is the foundation of a prosperous society. This essay examines the relationship between access to education and economic development.",
      } as SubmissionAnswer,
    },
    // Session 2 (in_progress B1): partial answers
    {
      sessionId: sessions[1].id,
      questionId: listeningQ,
      answer: objectiveAnswer(5),
    },
    {
      sessionId: sessions[1].id,
      questionId: readingQ,
      answer: objectiveAnswer(3),
    },
    // Session 3 (completed B1): listening + reading
    {
      sessionId: sessions[2].id,
      questionId: listeningQ,
      answer: objectiveAnswer(10),
      isCorrect: false,
    },
    {
      sessionId: sessions[2].id,
      questionId: readingQ,
      answer: objectiveAnswer(10),
      isCorrect: true,
    },
    // Session 4 (placement): listening + reading answers
    {
      sessionId: sessions[3].id,
      questionId: listeningQ,
      answer: objectiveAnswer(10),
      isCorrect: true,
    },
    {
      sessionId: sessions[3].id,
      questionId: readingQ,
      answer: objectiveAnswer(10),
      isCorrect: true,
    },
  ];

  await db.insert(table.examAnswers).values(answerData);
  logResult("Exam answers", answerData.length);

  // Link writing/speaking submissions to completed sessions via examSubmissions
  // Find writing and speaking submissions from learner 1 that are completed or review_pending
  const learner1WritingSub = submissions.all.find(
    (s) =>
      s.userId === learner1 &&
      s.skill === "writing" &&
      s.status === "completed",
  );
  const learner1SpeakingSub = submissions.all.find(
    (s) => s.userId === learner1 && s.skill === "speaking",
  );

  const examSubmissionData: NewExamSubmission[] = [];

  if (learner1WritingSub) {
    examSubmissionData.push({
      sessionId: sessions[0].id,
      submissionId: learner1WritingSub.id,
      skill: "writing",
      part: 2,
    });
  }
  if (learner1SpeakingSub) {
    examSubmissionData.push({
      sessionId: sessions[0].id,
      submissionId: learner1SpeakingSub.id,
      skill: "speaking",
      part: 1,
    });
  }

  // Link learner 2's writing submission to session 3
  const learner2WritingSub = submissions.all.find(
    (s) => s.userId === learner2 && s.skill === "writing",
  );
  if (learner2WritingSub) {
    examSubmissionData.push({
      sessionId: sessions[2].id,
      submissionId: learner2WritingSub.id,
      skill: "writing",
      part: 2,
    });
  }

  // Link learner 2's speaking submission to session 3
  const learner2SpeakingSub = submissions.all.find(
    (s) => s.userId === learner2 && s.skill === "speaking",
  );
  if (learner2SpeakingSub) {
    examSubmissionData.push({
      sessionId: sessions[2].id,
      submissionId: learner2SpeakingSub.id,
      skill: "speaking",
      part: 1,
    });
  }

  if (examSubmissionData.length > 0) {
    await db.insert(table.examSubmissions).values(examSubmissionData);
  }
  logResult("Exam submissions (linked)", examSubmissionData.length);

  return { all: sessions };
}
