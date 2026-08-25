"use client";

import Link from "next/link";
import React, { useEffect, useState } from "react";
import CourseSidebarWidgetTwo from "./CourseSidebarWidgetTwo";
import CourseDetailsBreadcrumb from "../../../common/Breadcrumb/CourseDetailsBreadcrumb";
import CourseContentDropdown from "./CourseContentDropdown";
import WhatYouLearnTwo from "./WhatYouLearnTwo";
import DetailsCourseInstructorTwo from "./DetailsCourseInstructorTwo";
import CourseRating from "./CourseRating";
import CourseCommonDetailsContent from "./CourseCommonDetailsContent";
import CommonCourseReview from "@/components/common/course-details/CommonCourseReview";
import CommonReviewForm from "@/components/common/course-details/CommonReviewForm";

const sections = [
  { id: "overview", label: "Summary" },
  { id: "courseContent", label: "Content" },
  { id: "details", label: "Outline" },
  { id: "intructor", label: "Instructor" },
  { id: "review", label: "Review" },
];

const CourseDetailsTwoMain: React.FC = () => {
  const [activeSection, setActiveSection] = useState<string>("overview");

  useEffect(() => {
    const handleScroll = () => {
      let currentSection = "overview";
      sections.forEach(({ id }) => {
        const sectionElement = document.getElementById(id);
        if (sectionElement) {
          const { top } = sectionElement.getBoundingClientRect();
          if (top < 150) currentSection = id; 
        }
      });
      setActiveSection(currentSection);
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const handleClick = (id: string) => {
    const section = document.getElementById(id);
    if (section) {
      window.scrollTo({
        top: section.offsetTop - 80, 
        behavior: "smooth",
      });
    }
  };

  return (
    <>
      <CourseDetailsBreadcrumb
        subtitle="Courses Details 02"
        description="Learn the essential principles and techniques of graphic design. Master typography, layout, color theory, and visual branding to create stunning designs."
      />

      {/* -- course details area start -- */}
      <section className="bd-course-details-area section-space-bottom">
        <div className="container">
          <div className="row gy-30">
            <div className="col-lg-8 col-md-12">
              <div className="bd-course-menu mt-30">
                <nav className="bd-course-menu-nav">
                  <ul>
                    {sections.map(({ id, label }) => (
                      <li key={id} className={activeSection === id ? "active" : ""}>
                        <Link href={`#${id}`} onClick={(e) => {
                          e.preventDefault();
                          handleClick(id);
                        }}>
                          {label}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </nav>
              </div>
              <div className="course-details-content">
                <div id="overview"><CourseCommonDetailsContent /></div>
                <div id="courseContent"><CourseContentDropdown /></div>
                <div id="details"><WhatYouLearnTwo /></div>
                <div id="intructor"><DetailsCourseInstructorTwo /></div>
                <div id="review"><CourseRating /></div>
                <div className="bd-course-feature-box mt-30">
                  <h3 className="bd-course-details-content-title">Course Review</h3>
                  <CommonCourseReview />
                  <CommonReviewForm />
                </div>
              </div>
            </div>
            <div className="col-lg-4 col-md-12">
              <CourseSidebarWidgetTwo />
            </div>
          </div>
        </div>
      </section>
      {/* -- course details area end -- */}
    </>
  );
};

export default CourseDetailsTwoMain;
