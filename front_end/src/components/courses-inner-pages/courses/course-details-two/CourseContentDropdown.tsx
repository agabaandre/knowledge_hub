
interface ICourseContent {
    id: string;
    title: string;
    duration: string;
    lesson: string;
    lessonDuration: string;
}
import Link from 'next/link';
import React from 'react';

const courseContent: ICourseContent[] = [
    { id: 'One', title: 'Introduction to Graphic Design', duration: '00 hr | 20 min | 30 s', lesson: 'Basics of Graphic Design', lessonDuration: '10:15' },
    { id: 'Two', title: 'Design Principles', duration: '00 hr | 25 min | 45 s', lesson: 'Elements of Design', lessonDuration: '12:30' },
    { id: 'Three', title: 'Typography Basics', duration: '00 hr | 15 min | 20 s', lesson: 'Understanding Fonts and Typefaces', lessonDuration: '09:00' },
    { id: 'Four', title: 'Color Theory', duration: '00 hr | 30 min | 00 s', lesson: 'Color Theory and Application', lessonDuration: '14:30' },
    { id: 'Five', title: 'Tools and Software', duration: '00 hr | 20 min | 10 s', lesson: 'Introduction to Design Software', lessonDuration: '11:45' },
    { id: 'Six', title: 'Creating Graphics', duration: '00 hr | 25 min | 50 s', lesson: 'Designing Logos and Icons', lessonDuration: '13:20' },
    { id: 'Seven', title: 'Advanced Techniques', duration: '00 hr | 35 min | 25 s', lesson: 'Advanced Graphic Design Techniques', lessonDuration: '16:00' }
];

const CourseContentDropdown = () => {
    return (
        <div className="bd-course-feature-box mt-30" id="courseContent">
            <h3 className="bd-course-details-content-title">Course Content</h3>
            <div className="bd-course-curriculum-accordion">
                <div className="accordion-common-style accordion-transparent accordion-sl-number accordion-item-margin">
                    <div className="accordion" id="accordionExampleEight">
                        {courseContent.map((item, index) => (
                            <div className="accordion-item" key={item.id}>
                                <h2 className="accordion-header" id="headingOne">
                                    <button className={`accordion-button ${index === 0 ? '' : 'collapsed'}`}
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target={`#collapse${item.id}`}
                                        aria-expanded={index === 0 ? "true" : "false"}
                                        aria-controls={`collapse${item.id}`}
                                    >
                                        <span className="accordion-button-title">
                                            {item.title}
                                            <span>{item.duration}</span>
                                        </span>
                                    </button>
                                </h2>
                                <div
                                    id={`collapse${item.id}`}
                                    className={`accordion-collapse collapse ${index === 0 ? 'show' : ''}`}
                                    aria-labelledby={`heading${item.id}`}
                                    data-bs-parent="#accordionExampleEight"
                                >
                                    <div className="accordion-body">
                                        <Link href="#" className="bd-course-curriculum-content d-flex-between">
                                            <div className="bd-course-curriculum-info d-flex-items gap-10">
                                                <div className="icon"><i className="fa-solid fa-video"></i></div>
                                                <p className="title">{item.lesson}</p>
                                            </div>
                                            <div className="bd-course-curriculum-meta d-flex-items gap-10">
                                                <span className="duration">{item.lessonDuration}</span>
                                                <span className="status"><i className="fa-solid fa-lock"></i></span>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CourseContentDropdown;
