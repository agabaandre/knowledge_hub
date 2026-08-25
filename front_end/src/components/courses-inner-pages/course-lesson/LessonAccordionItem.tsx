import Link from 'next/link';
import React, { useEffect, useState } from 'react';

// Define the type for the lesson and section
interface Lesson {
    title: string;
    duration: string;
    isLocked: boolean;
}

interface Section {
    id: string;
    title: string;
    lessons: Lesson[];
}

interface LessonAccordionItemProps {
    section: Section;
    index: number; 
}

const LessonAccordionItem: React.FC<LessonAccordionItemProps> = ({ section, index }) => {
    const [active, setActive] = useState(false);

    useEffect(() => {
        // Set the first item to be active by default
        if (index === 0) {
            setActive(true);
        }
    }, [index]);

    return (
        <>
            <div className={`accordion-item ${active ? 'active' : ''}`}>
                <h2 className="accordion-header" id={`heading${section.id}`}>
                    <button
                        className={`accordion-button ${active ? '' : 'collapsed'}`}
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target={`#collapse${section.id}`}
                        aria-expanded={active ? 'true' : 'false'}
                        aria-controls={`collapse${section.id}`}
                    >
                        <span>Q.</span> {section.title}
                    </button>
                </h2>
                <div
                    id={`collapse${section.id}`}
                    className={`accordion-collapse collapse ${active ? 'show' : ''}`}
                    aria-labelledby={`heading${section.id}`}
                    data-bs-parent="#accordionExample"
                >
                    <div className="accordion-body">
                        {section.lessons.map((lesson, index) => (
                            <Link href="#" key={index} className="bd-course-curriculum-content d-flex-between">
                                <div className="bd-course-curriculum-info d-flex-items gap-10">
                                    <div className="icon">
                                        <i className="fa-solid fa-video"></i>
                                    </div>
                                    <p className="title">{lesson.title}</p>
                                </div>
                                <div className="bd-course-curriculum-meta d-flex-items gap-10">
                                    <span className="duration">{lesson.duration}</span>
                                    <span className="status">
                                        <i className={`fa-solid ${lesson.isLocked ? 'fa-lock' : 'fa-play'}`}></i>
                                    </span>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
};

export default LessonAccordionItem;