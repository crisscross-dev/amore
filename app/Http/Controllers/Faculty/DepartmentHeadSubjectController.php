<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Admin\SubjectController;

class DepartmentHeadSubjectController extends SubjectController
{
    protected string $viewBase = 'department_head.subjects.';

    protected string $routeBase = 'department-head.subjects.';
}
