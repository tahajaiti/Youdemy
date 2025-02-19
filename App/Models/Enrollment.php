<?php


class Enrollment
{

    protected Database $pdo;
    private int $course_id;
    private int $student_id;

    public function __construct(int $course, int $user)
    {
        $this->course_id = $course;
        $this->student_id = $user;
        $this->pdo = Database::getInstance();
    }

    public function getCourse()
    {
        return $this->course_id;
    }

    public function setCourse(int $course)
    {
        $this->course_id = $course;
    }

    public function getStudent()
    {
        return $this->student_id;
    }


    public function setStudent($student)
    {
        $this->student_id = $student;
    }

    public function enroll(): bool
    {
        $sql = "INSERT INTO enrollments(student_id, course_id) VALUES (:uid, :cid)";
        return $this->pdo->execute($sql, [
            ':uid' => $this->student_id,
            ':cid' => $this->course_id
        ]);
    }

    public function getEnrolledCourses(int $page = 1, int $limit = 8): array
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT c.*
            FROM courses c
            JOIN enrollments e ON c.id = e.course_id
            JOIN users u ON e.student_id = u.id
            WHERE u.id = :id
            LIMIT :limit OFFSET :offset";

        $data = $this->pdo->fetchAll($sql, [
            ':id' => $this->student_id,
            ':limit' => $limit,
            ':offset' => $offset,
        ]);

        $totalCountSql = "SELECT COUNT(*) as total 
                    FROM courses c
                    JOIN enrollments e ON c.id = e.course_id
                    JOIN users u ON e.student_id = u.id
                    WHERE u.id = :id";
        $totalRes = $this->pdo->fetch($totalCountSql, [':id' => $this->student_id]);

        $pageCount = ceil($totalRes['total'] / $limit);

        $courses = [];
        foreach ($data as $row) {
            $course = new StudentCourse();
            $course->setId($row['id']);
            $course->setTitle($row['title']);
            $course->setDescription($row['description']);
            $course->setContent($row['content']);
            $course->setImage($row['image']);

            $teacher = new User();
            $teacher->setId($row['teacher_id']);
            $course->setTeacher($teacher);

            $category = new Category();
            $category->setId($row['category_id']);
            $course->setCategory($category);

            $courseTag = new CourseTag();
            $courseTag->setCourseId($row['id']);
            $tags = $courseTag->getTagsByCourse();
            $course->setTags($tags);

            $courses[] = $course;
        }

        return [
            'courses' => $courses,
            'pagination' => [
                'page' => $page,
                'total_pages' => $pageCount,
            ],
        ];
    }
    public function getCourseStudents(): array
    {
        $sql = "SELECT u.id, u.name, u.email, u.role FROM users u
                JOIN enrollments e ON u.id = e.student_id
                WHERE e.course_id = :cid";
        $data = $this->pdo->fetchAll($sql, [':cid' => $this->course_id]);

        $users = [];

        foreach ($data as $row) {
            $student = new User();
            $student->setId($row['id']);
            $student->setName($row['name']);
            $student->setEmail($row['email']);
            $student->setRole($row['role']);

            $users[] = $student;
        }

        return $users;
    }
    public static function isEnrolled(int $student_id, int $course_id): bool
    {
        $pdo = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
        $result = $pdo->fetchCol($sql, [
            ':student_id' => $student_id,
            ':course_id' => $course_id,
        ]);
        return $result > 0;
    }

    public function getTotalStudents(): int
    {
        $sql = "SELECT COUNT(DISTINCT e.student_id) as total_students
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE c.teacher_id = :t_id";

        $result = $this->pdo->fetch($sql, [':t_id' => Session::getId()]);
        return (int) $result['total_students'];
    }

    public function getTotalCourses(): int
    {
        $sql = "SELECT COUNT(DISTINCT c.id) as total_courses FROM courses c 
                JOIN users u ON c.teacher_id = u.id
                WHERE c.teacher_id = :t_id";

        $result = $this->pdo->fetch($sql, [':t_id' => Session::getId()]);
        return (int) $result['total_courses'];
    }
}
