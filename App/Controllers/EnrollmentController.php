<?php

class EnrollmentController extends Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function enroll()
    {
        if (!$this->validateToken($_GET['csrf'])) {
            $_SESSION['error'] = 'Invalid CSRF token.';
            $this->redirect('/catalog');
        }

        $course_id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $user_id = Session::getId();

        if (!$course_id || $user_id === 0 || Session::getRole() !== 'student') {
            $_SESSION['error'] = 'Invalid course/session.';
            $this->redirect('/catalog');
        }

        $enroll = new Enrollment($course_id, $user_id);

        if ($enroll->enroll()) {
            $_SESSION['success'] = 'Enrolled!';
            $this->redirect('/catalog');
        } else {
            $_SESSION['error'] = 'Failed to enroll, Try again later.';
            $this->redirect('/catalog');
        }
    }

    public function getAll()
    {
        $page = intval($_GET['p'] ?? 1);

        $user_id = Session::getId();
        if ($user_id === 0 || Session::getRole() !== 'student') {
            $_SESSION['error'] = 'Invalid session.';
            $this->redirect('/mycourses');
        }

        $enroll = new Enrollment(0,$user_id);

        $result = $enroll->getEnrolledCourses($page);

        return [
            'courses' => $result['courses'],
            'pagination' => $result['pagination']
        ];
    }

    public function getCourseStudents(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id){
            $_SESSION['error'] = 'Invalid id.';
            $this->redirect('/mystats');
        }

        $enroll = new Enrollment($id, 0);

        return $enroll->getCourseStudents();
    }
}
