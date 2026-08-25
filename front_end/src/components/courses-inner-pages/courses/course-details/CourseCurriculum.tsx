import curriculamData from "@/data/courses/course-curriculam-data";
import Link from "next/link";
import React from "react";

const CourseCurriculum: React.FC = () => {
    return (
        <div className="bd-course-curriculum mb-30">
            <h3 className="bd-course-details-content-title">Curriculum</h3>
            <span className="bd-course-curriculum-overview">40 lectures | 10h 15m</span>
            <div className="accordion-common-style accordion-transparent">
                <div className="accordion" id="accordionExample">
                    {curriculamData.map((section, index) => (
                        <div className="accordion-item" key={index}>
                            <h2 className="accordion-header" id={`heading${index}`}>
                                <button
                                    className="accordion-button"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target={`#collapse${index}`}
                                    aria-expanded={index === 0}
                                    aria-controls={`collapse${index}`}
                                >
                                    <span>Q.</span> {section.title}
                                </button>
                            </h2>
                            <div
                                id={`collapse${index}`}
                                className={`accordion-collapse collapse ${index === 0 ? "show" : ""}`}
                                aria-labelledby={`heading${index}`}
                                data-bs-parent="#accordionExample"
                            >
                                <div className="accordion-body">
                                    {section.lectures.map((lecture, lectureIndex) => (
                                        <Link key={lectureIndex} href="#" className="bd-course-curriculum-content d-flex-between">
                                            <div className="bd-course-curriculum-info d-flex-items gap-10">
                                                <div className="icon">
                                                    <i className="fa-solid fa-video"></i>
                                                </div>
                                                <p className="title">{lecture.title}</p>
                                            </div>
                                            <div className="bd-course-curriculum-meta d-flex-items gap-10">
                                                <span className="duration">{lecture.duration}</span>
                                                <span className="status">
                                                    <i className="fa-solid fa-lock"></i>
                                                </span>
                                            </div>
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default CourseCurriculum;
