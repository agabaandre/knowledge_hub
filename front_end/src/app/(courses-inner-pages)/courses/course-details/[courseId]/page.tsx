import CoursesDetailsMain from "@/components/courses-inner-pages/courses/course-details/CoursesDetailsMain";
import Wrapper from "@/layout/DefaultWrapper";
import { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
  title: "Course Details - Education & Online Courses React NextJs Template",
};

interface PageProps {
  params: Promise<{ courseId: number }>;
}

const CourseDetails = async (props: PageProps) => {
  const resolvedParams = await props.params;
  const { courseId } = resolvedParams;

  return (
    <>
      <Wrapper>
        <main>
          <CoursesDetailsMain courseId={courseId} />
        </main>
      </Wrapper>
    </>
  );
};

export default CourseDetails;
