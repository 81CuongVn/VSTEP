<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Để trống nội dung ở đây
        // Vì bảng failed_jobs đã có khóa chính, lệnh tạo thêm ID sẽ gây lỗi trên MySQL
    }

    public function down(): void
    {
        // Để trống nội dung ở đây
    }
};