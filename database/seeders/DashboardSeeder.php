<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Lecture;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\EducationProgram;
use App\Models\TeachingDuty;
use App\Models\Room;
use App\Models\Exam;
use App\Models\ExamProctoring;
use App\Models\Meeting;
use App\Models\Workload;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // Tạo năm học và học kỳ
        $academicYear = AcademicYear::create([
             
            'academic_year_code' => '2024-2025',
            'academic_year_name' => 'Năm học 2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-05-31',
            'status' => 'active'
        ]);

        $semester = Semester::create([
             
            'semester_code' => 'HK1',
            'semester_name' => 'Học kỳ 1',
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31',
            'status' => 'active'
        ]);

        // Tạo trường đại học
        $university = University::create([
            'university_code' => 'TDMU',
            'university_name' => 'Trường Đại học Thủ Dầu Một',
            'university_type' => 'Công lập',
            'address' => 'Số 06 Trần Văn Ơn, Phú Hòa, TP. Thủ Dầu Một, Bình Dương',
            'phone' => '(0274) 3834 694',
            'email' => 'info@tdmu.edu.vn',
            'website' => 'https://tdmu.edu.vn',
            'description' => 'Trường Đại học Thủ Dầu Một là cơ sở giáo dục đại học công lập, trực thuộc UBND tỉnh Bình Dương.',
            'status_id' => 1 
        ]);

        // Tạo khoa
        $faculty = Faculty::create([
            'faculty_code' => 'CNTT',
            'faculty_name' => 'Khoa Công nghệ thông tin',
            'description' => 'Khoa Công nghệ thông tin',
            'university_id' => $university->id,
            'status_id' => 1
        ]);

        // Tạo bộ môn
        $department = Department::create([
            'department_code' => 'KTPM',
            'department_name' => 'Bộ môn Kỹ thuật phần mềm',
            'description' => 'Bộ môn Kỹ thuật phần mềm',
            'faculty_id' => $faculty->id,
            'status_id' => 1
        ]);

        // Tạo giảng viên
        $lecture = Lecture::create([
             
            'lecturer_code' => 'GV001',
            'full_name' => 'Nguyễn Văn A',
            'degree' => 'Thạc sĩ',
            'major' => 'Công nghệ thông tin',
            'email' => 'nguyenvana@tdmu.edu.vn',
            'phone' => '0123456789',
            'department_id' => $department->id,
            'university_id' => $university->id
        ]);

        // Tạo roles
        $superadminRole = Role::create([
             
            'role_name' => 'superadmin',
            'description' => 'Super Admin'
        ]);

        $adminRole = Role::create([
             
            'role_name' => 'admin',
            'description' => 'Admin trường'
        ]);

        $truongkhoaRole = Role::create([
             
            'role_name' => 'truongkhoa',
            'description' => 'Trưởng khoa'
        ]);

        $truongbomonRole = Role::create([
             
            'role_name' => 'truongbomon',
            'description' => 'Trưởng bộ môn'
        ]);

        $giangvienRole = Role::create([
             
            'role_name' => 'giangvien',
            'description' => 'Giảng viên'
        ]);

        // Tạo users
        $superadminUser = User::create([
             
            'email' => 'superadmin@tdmu.edu.vn',
            'password_hash' => bcrypt('password'),
            'status_id' => 1,
            'user_type' => 'superadmin'
        ]);

        $adminUser = User::create([
             
            'email' => 'admin@tdmu.edu.vn',
            'password_hash' => bcrypt('password'),
            'status_id' => 1,
            'user_type' => 'admin',
            'lecture_id' => $lecture->id
        ]);

        $truongkhoaUser = User::create([
             
            'email' => 'truongkhoa@tdmu.edu.vn',
            'password_hash' => bcrypt('password'),
            'status_id' => 1,
            'user_type' => 'truongkhoa',
            'lecture_id' => $lecture->id
        ]);

        $truongbomonUser = User::create([
             
            'email' => 'truongbomon@tdmu.edu.vn',
            'password_hash' => bcrypt('password'),
            'status_id' => 1,
            'user_type' => 'truongbomon',
            'lecture_id' => $lecture->id
        ]);

        $giangvienUser = User::create([
             
            'email' => 'giangvien@tdmu.edu.vn',
            'password_hash' => bcrypt('password'),
            'status_id' => 1,
            'user_type' => 'giangvien',
            'lecture_id' => $lecture->id
        ]);

        // Tạo chương trình đào tạo
        $educationProgram = EducationProgram::create([
             
            'program_code' => 'CNTT2024',
            'program_name' => 'Chương trình đào tạo CNTT 2024',
            'description' => 'Chương trình đào tạo Công nghệ thông tin',
            'university_id' => $university->id,
            'faculty_id' => $faculty->id,
            'department_id' => $department->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id
        ]);

        // Tạo môn học
        $course = Course::create([
             
            'course_code' => 'CS101',
            'course_name' => 'Lập trình cơ bản',
            'credits' => 3,
            'description' => 'Môn học lập trình cơ bản',
            'program_id' => $educationProgram->id
        ]);

        // Tạo phòng học
        $room = Room::create([
             
            'room_code' => 'A101',
            'room_name' => 'Phòng A101',
            'room_type' => 'Phòng học',
            'capacity' => 50,
            'building' => 'Tòa A',
            'floor' => 1,
            'university_id' => $university->id,
            'status' => 'active'
        ]);

        // Tạo lịch giảng
        TeachingDuty::create([
             
            'lecture_id' => $lecture->id,
            'course_id' => $course->id,
            'room_id' => $room->id,
            'teaching_date' => now()->addDays(1),
            'start_time' => now()->addDays(1)->setTime(8, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'description' => 'Buổi học lập trình cơ bản',
            'status' => 'scheduled'
        ]);

        // Tạo kỳ thi
        $exam = Exam::create([
             
            'exam_code' => 'EX001',
            'exam_name' => 'Thi cuối kỳ Lập trình cơ bản',
            'exam_type' => 'Cuối kỳ',
            'exam_date' => now()->addDays(7),
            'start_time' => now()->addDays(7)->setTime(8, 0),
            'end_time' => now()->addDays(7)->setTime(10, 0),
            'description' => 'Thi cuối kỳ môn Lập trình cơ bản',
            'university_id' => $university->id,
            'status' => 'scheduled'
        ]);

        // Tạo lịch coi thi
        ExamProctoring::create([
             
            'exam_id' => $exam->id,
            'lecture_id' => $lecture->id,
            'room_id' => $room->id,
            'exam_date' => now()->addDays(7),
            'start_time' => now()->addDays(7)->setTime(8, 0),
            'end_time' => now()->addDays(7)->setTime(10, 0),
            'role' => 'proctor',
            'status' => 'scheduled'
        ]);

        // Tạo cuộc họp
        $meeting = Meeting::create([
             
            'meeting_title' => 'Họp bộ môn tháng 9',
            'meeting_date' => now()->addDays(3),
            'start_time' => now()->addDays(3)->setTime(14, 0),
            'end_time' => now()->addDays(3)->setTime(16, 0),
            'location' => 'Phòng họp A101',
            'agenda' => 'Thảo luận kế hoạch giảng dạy học kỳ 1',
            'department_id' => $department->id,
            'status' => 'scheduled'
        ]);

        // Tạo workload
        Workload::create([
             
            'lecture_id' => $lecture->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'hours' => 120,
            'description' => 'Khối lượng giảng dạy học kỳ 1',
            'status' => 'active'
        ]);

        // Tạo audit log
        AuditLog::create([
             
            'user_id' => $adminUser->id,
            'action' => 'Tạo giảng viên mới',
            'table_name' => 'lectures',
            'record_id' => $lecture->id,
            'old_values' => null,
            'new_values' => $lecture->toArray(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder'
        ]);
    }
} 