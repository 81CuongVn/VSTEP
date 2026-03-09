import { env } from "@common/env";
import { AuthErrors } from "@common/schemas";
import { Elysia, t } from "elysia";
import { authPlugin } from "@/plugins/auth";

export const ai = new Elysia({
  name: "module:ai",
  prefix: "/ai",
  detail: { tags: ["AI"] },
})
  .use(authPlugin)

  .post(
    "/paraphrase",
    async ({ body }) => {
      const res = await fetch(`${env.GRADING_SERVICE_URL}/ai/paraphrase`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
      });
      if (!res.ok) {
        throw new Error(`Grading service error: ${res.status}`);
      }
      return res.json();
    },
    {
      auth: true,
      body: t.Object({
        text: t.String({ minLength: 1 }),
        skill: t.Union([
          t.Literal("listening"),
          t.Literal("reading"),
          t.Literal("writing"),
          t.Literal("speaking"),
        ]),
        context: t.Optional(t.String()),
      }),
      response: {
        200: t.Object({
          highlights: t.Array(
            t.Object({
              phrase: t.String(),
              note: t.String(),
            }),
          ),
        }),
        ...AuthErrors,
      },
      detail: {
        summary: "AI Paraphrase",
        description:
          "Analyze text and return paraphrase suggestions for key phrases.",
        security: [{ bearerAuth: [] }],
      },
    },
  )

  .post(
    "/explain",
    async ({ body }) => {
      const res = await fetch(`${env.GRADING_SERVICE_URL}/ai/explain`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          text: body.text,
          skill: body.skill,
          question_numbers: body.questionNumbers,
          answers: body.answers,
          correct_answers: body.correctAnswers,
        }),
      });
      if (!res.ok) {
        throw new Error(`Grading service error: ${res.status}`);
      }
      return res.json();
    },
    {
      auth: true,
      body: t.Object({
        text: t.String({ minLength: 1 }),
        skill: t.Union([
          t.Literal("listening"),
          t.Literal("reading"),
          t.Literal("writing"),
          t.Literal("speaking"),
        ]),
        questionNumbers: t.Optional(t.Array(t.Number())),
        answers: t.Optional(t.Record(t.String(), t.String())),
        correctAnswers: t.Optional(t.Record(t.String(), t.String())),
      }),
      response: {
        200: t.Object({
          highlights: t.Array(
            t.Object({
              phrase: t.String(),
              note: t.String(),
              category: t.Union([
                t.Literal("grammar"),
                t.Literal("vocabulary"),
                t.Literal("strategy"),
                t.Literal("discourse"),
              ]),
            }),
          ),
          questionExplanations: t.Optional(
            t.Array(
              t.Object({
                questionNumber: t.Number(),
                correctAnswer: t.String(),
                explanation: t.String(),
                wrongAnswerNote: t.Optional(t.String()),
              }),
            ),
          ),
        }),
        ...AuthErrors,
      },
      detail: {
        summary: "AI Explain",
        description:
          "Analyze text for grammar, vocabulary, and strategy with Vietnamese explanations.",
        security: [{ bearerAuth: [] }],
      },
    },
  );
