import CoursesLassonMain from '@/components/courses-inner-pages/course-lesson/CoursesLassonMain';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Course Lesson - Education & Online Courses React NextJs Template",
};

const CourseLesson = () => {
    return (
        <>
            <main>
                <CoursesLassonMain />
            </main>
        </>
    );
};

export default CourseLesson;