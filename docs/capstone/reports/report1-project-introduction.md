# I. Record of Changes

| Version | Date | Author | Description |
|---------|------|--------|-------------|
| 1.0 | 10/03/2026 | Hoàng Văn Anh Nghĩa | Initial version |

# II. Project Introduction

## 1. Overview

### 1.1 Project Information

| Item | Detail |
|------|--------|
| Project Name (EN) | An Adaptive VSTEP Preparation System with Comprehensive Skill Assessment and Personalized Learning Support |
| Project Name (VN) | Hệ Thống Luyện Thi VSTEP Thích Ứng Với Đánh Giá Toàn Diện Kỹ Năng Và Hỗ Trợ Học Tập Cá Nhân Hóa |
| Project Code | SP26SE145 |
| Group Name | GSP26SE63 |
| Software Type | Web Application |
| Duration | 01/01/2026 – 30/04/2026 |
| Academic Supervisor | Lâm Hữu Khánh Phương (phuonglhk@fe.edu.vn) |
| Industry Supervisor | Trần Trọng Huỳnh (huynhtt4@fe.edu.vn) |

### 1.2 Project Team

| Full Name | Student ID | Role | Email |
|-----------|------------|------|-------|
| Hoàng Văn Anh Nghĩa | SE172605 | Team Leader | nghiahvase172605@fpt.edu.vn |
| Nguyễn Minh Khôi | SE172625 | Developer | khoinmse172625@fpt.edu.vn |
| Nguyễn Nhật Phát | SE172607 | Developer | phatnnse172607@fpt.edu.vn |
| Nguyễn Trần Tấn Phát | SE173198 | Developer | phatnttse173198@fpt.edu.vn |

## 2. Product Background

Kỳ thi VSTEP (Vietnamese Standardized Test of English Proficiency) là kỳ thi đánh giá năng lực tiếng Anh theo Khung năng lực ngoại ngữ 6 bậc dùng cho Việt Nam, được Bộ Giáo dục và Đào tạo công nhận theo Quyết định số 729/QĐ-BGDĐT. VSTEP đánh giá đầy đủ 4 kỹ năng Nghe, Nói, Đọc, Viết với các cấp độ từ A1 đến C1, và hiện là chứng chỉ bắt buộc cho điều kiện đầu ra tại nhiều trường đại học, cũng như yêu cầu tuyển dụng và thăng tiến trong khu vực công.

Hiện tại, người học VSTEP gặp nhiều khó khăn trong quá trình ôn luyện. Trình độ giữa 4 kỹ năng thường không đồng đều — người học có thể đạt B2 ở Đọc nhưng chỉ A2 ở Nói. Tài liệu ôn luyện phần lớn là tĩnh, không điều chỉnh theo trình độ thực tế của từng người. Đặc biệt, hai kỹ năng khó nhất là Viết và Nói lại thiếu công cụ đánh giá và phản hồi tức thì, khiến người học dễ lặp lại sai lầm mà không nhận ra.

Dự án này xây dựng một hệ thống ôn luyện VSTEP thích ứng, kết hợp luyện tập chuyên sâu với thi thử giả lập, sử dụng AI để cung cấp phản hồi nhanh cho kỹ năng Viết và Nói, đồng thời cá nhân hóa lộ trình học tập dựa trên trình độ và mục tiêu của từng người học.

## 3. Existing Systems

### 3.1 Traditional VSTEP Preparation Methods

Các trung tâm luyện thi trực tiếp và giáo trình ôn luyện truyền thống với chương trình cố định cho tất cả học viên.

- **Ưu điểm:** Nội dung bám sát cấu trúc đề thi chính thức; có tương tác trực tiếp với giảng viên.
- **Nhược điểm:** Thiếu cá nhân hóa; không theo dõi được tiến độ từng kỹ năng; thời gian cố định, không phù hợp với người bận rộn.

### 3.2 General English Learning Applications

Ví dụ: Duolingo (duolingo.com), ELSA Speak (elsaspeak.com).

- **Ưu điểm:** Tính tương tác cao với gamification; dễ tiếp cận, chi phí thấp.
- **Nhược điểm:** Nội dung không thiết kế cho VSTEP; mất cân bằng kỹ năng (ELSA chỉ tập trung Nói, Duolingo thiếu Viết và Đọc học thuật B2-C1).

### 3.3 VSTEP Mock Test Platforms

Ví dụ: luyenthivstep.vn, vstepmaster.edu.vn, tienganh123.com.

- **Ưu điểm:** Giao diện mô phỏng thi máy tính; kết quả tức thì cho Nghe và Đọc; kho đề thi lớn.
- **Nhược điểm:** Thiếu AI đánh giá cho Viết và Nói; không có lộ trình học thích ứng; chỉ hiển thị điểm số, không phân tích chi tiết.

### 3.4 AI Writing & Speaking Platforms

Ví dụ: Grammarly (grammarly.com), Write & Improve by Cambridge (writeandimprove.com).

- **Ưu điểm:** AI phản hồi tức thì cho grammar và pronunciation; công nghệ tiên tiến, UX tốt.
- **Nhược điểm:** Không theo rubric VSTEP; chỉ tập trung 1-2 kỹ năng; không có mock test theo format VSTEP.

### 3.5 IELTS/TOEFL Preparation Platforms

Ví dụ: Magoosh (magoosh.com), British Council - Road to IELTS (takeielts.britishcouncil.org).

- **Ưu điểm:** Mô hình adaptive learning đã được chứng minh; hệ sinh thái hoàn chỉnh.
- **Nhược điểm:** Format và rubric khác VSTEP hoàn toàn; chi phí cao ($100-200/năm); không phục vụ mục tiêu chứng chỉ Việt Nam.

### 3.6 Comparative Analysis Summary

| Tiêu chí | Truyền thống | Duolingo / ELSA | Thi thử online | AI Tools | IELTS Prep | Hệ thống đề xuất |
|----------|-------------|-----------------|----------------|----------|------------|------------------|
| Cá nhân hóa | Không | Một phần | Không | Một phần | Có | **Có** |
| Đánh giá 4 kỹ năng | Có | Không | 2/4 | Không | Có | **Có** |
| Phản hồi tức thì | Không | Có | MCQ only | Có | Một phần | **Có** |
| Theo dõi tiến độ | Không | Cơ bản | Không | Không | Một phần | **Có** |
| Phù hợp VSTEP | Có | Không | Có | Không | Không | **Có** |

Chưa có giải pháp nào kết hợp được cả 3 yếu tố: phù hợp VSTEP, cá nhân hóa adaptive, và đánh giá đầy đủ 4 kỹ năng với phản hồi tức thì.

## 4. Business Opportunity

Thị trường EdTech tại Việt Nam đang tăng trưởng mạnh, với quy mô ước đạt USD 1.1 tỷ vào năm 2025 và dự kiến đạt USD 3.2 tỷ vào năm 2034. Riêng phân khúc Digital English Learning đạt USD 43 triệu (2025), dự kiến tăng lên USD 120.6 triệu vào năm 2033. Các yếu tố thúc đẩy bao gồm: tỷ lệ người dùng Internet cao (79.1%), chi tiêu giáo dục của hộ gia đình Việt Nam ở mức 20-24% tổng chi tiêu (cao nhất ASEAN), và chính sách chuyển đổi số giáo dục của Chính phủ.

Trong bối cảnh đó, thị trường ôn luyện VSTEP bộc lộ lỗ hổng rõ rệt. Lớp học truyền thống và sách sử dụng tài liệu tĩnh, thiếu phản hồi linh hoạt. Các website thi thử VSTEP chủ yếu là kho đề trắc nghiệm, bỏ ngỏ chấm Nói và Viết. Ứng dụng quốc tế như Duolingo hay Grammarly không bám sát cấu trúc đề VSTEP và không phục vụ mục tiêu chứng chỉ Việt Nam.

Hệ thống đề xuất tạo ra sự khác biệt bằng cách kết hợp: (1) Adaptive Scaffolding — điều chỉnh mức độ hỗ trợ theo trình độ người học, (2) Hybrid Grading — AI chấm nhanh kết hợp Instructor review cho kỹ năng Viết và Nói, (3) Trực quan hóa tiến độ với Spider Chart và Sliding Window, và (4) Multi-Goal Profiles — hỗ trợ nhiều mục tiêu học tập đồng thời.

## 5. Software Product Vision

Dành cho sinh viên đại học cần đạt chuẩn đầu ra và người đi làm cần chứng chỉ thăng tiến tại Việt Nam, những người đang gặp khó khăn với phương pháp ôn luyện VSTEP thiếu cá nhân hóa và phản hồi chậm, Hệ thống ôn luyện VSTEP thích ứng là một nền tảng web cung cấp lộ trình học cá nhân hóa, đánh giá 4 kỹ năng với phản hồi nhanh bằng AI, và trực quan hóa tiến độ học tập. Khác với các trang web thi thử tĩnh chỉ có đề và đáp án, hoặc ứng dụng tiếng Anh tổng quát không bám sát VSTEP, sản phẩm này kết hợp Adaptive Scaffolding, Hybrid Grading, và Analytics để thu hẹp khoảng cách kỹ năng hiệu quả.

## 6. Scope & Limitations

### 6.1 Major Features

**Phase 1 — MVP (Tháng 1-3):**

- FE-01: User Authentication — Đăng ký, đăng nhập, quản lý profile với các vai trò Learner, Instructor, Admin.
- FE-02: Placement Test — Bài kiểm tra đầu vào xác định trình độ ban đầu cho 4 kỹ năng.
- FE-03: Practice Mode - Listening — Luyện tập kỹ năng Nghe với Adaptive Scaffolding.
- FE-04: Practice Mode - Reading — Luyện tập kỹ năng Đọc với các dạng câu hỏi theo format VSTEP.
- FE-05: Practice Mode - Writing + AI Grading — Luyện tập Viết với phản hồi AI theo rubric VSTEP.
- FE-06: Practice Mode - Speaking + AI Grading — Luyện tập Nói với ghi âm và phản hồi AI.
- FE-07: Mock Test Mode — Thi thử giả lập đầy đủ 4 kỹ năng theo format và thời gian VSTEP.
- FE-08: Human Grading — Giao diện cho Instructor chấm điểm Writing và Speaking với rubric VSTEP.
- FE-09: Progress Tracking — Spider Chart hiển thị năng lực 4 kỹ năng và Sliding Window theo dõi tiến độ.
- FE-10: Learning Path — Lộ trình học tập cá nhân hóa dựa trên kết quả và tiến độ.
- FE-11: Goal Setting — Thiết lập mục tiêu (B1/B2/C1) và timeline, theo dõi tiến độ so với mục tiêu.

**Phase 2 — Enhancement (Tháng 4):**

- FE-12: Content Management — Admin quản lý ngân hàng câu hỏi và đề thi.
- FE-13: User Management — Admin quản lý tài khoản và phân quyền.
- FE-14: Analytics Dashboard — Báo cáo thống kê cho Instructor và Admin.
- FE-15: Notification System — Thông báo nhắc nhở học tập và kết quả bài thi.
- FE-16: Advanced Admin Features — Lịch sử hoạt động, phân công bài chấm, tùy chỉnh thông báo.

### 6.2 Limitations & Exclusions

- LI-01: Hệ thống chỉ hỗ trợ VSTEP format (B1-C1), không hỗ trợ các kỳ thi khác (IELTS, TOEFL, TOEIC).
- LI-02: AI Grading cho Writing và Speaking là công cụ hỗ trợ luyện tập, không thay thế đánh giá chính thức của Instructor.
- LI-03: Phiên bản MVP chỉ hỗ trợ tiếng Việt làm ngôn ngữ giao diện.
- LI-04: Hệ thống không tích hợp thanh toán online trong phiên bản MVP.
