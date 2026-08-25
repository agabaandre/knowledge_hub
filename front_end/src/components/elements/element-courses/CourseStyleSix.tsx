
import Image, { StaticImageData } from 'next/image';
import React from 'react';
import CourseThum1 from '../../../../public/assets/images/course/course-thumb-1.webp';
import CourseThum2 from '../../../../public/assets/images/course/course-thumb-2.webp';
import CourseThum3 from '../../../../public/assets/images/course/course-thumb-3.webp';
import shape1 from '../../../../public/assets/images/course/course-shape-1.webp';
import shape2 from '../../../../public/assets/images/course/course-shape-2.webp';
import shape3 from '../../../../public/assets/images/course/course-shape-3.webp';
import cscTextShape from '../../../../public/assets/images/course/csc.webp';
import bapTextShape from '../../../../public/assets/images/course/bap.webp';
import bbaTextShape from '../../../../public/assets/images/course/bba.webp';
import Link from 'next/link';

interface Course {
    title: string;
    description: string;
    duration: string;
    credits: string;
    image: StaticImageData;
    logo:StaticImageData;
    shape: StaticImageData;
    badge: string;
}

const courses: Course[] = [
    {
        title: 'BSc in Computer Science',
        description: 'Learn programming, algorithms, and computational theory to drive the future of technology.',
        duration: '4 Years',
        credits: '12 Credits',
        image: CourseThum1,
        logo:cscTextShape,
        shape: shape1,
        badge: 'FULL-TIME'
    },
    {
        title: 'Bachelor of Arts in Psychology',
        description: 'Understand human behavior and mental processes through a comprehensive study of psychology.',
        duration: '3 Years',
        credits: '10 Credits',
        image: CourseThum2,
        logo:bapTextShape,
        shape: shape2,
        badge: 'PART-TIME'
    },
    {
        title: 'Bachelor of Business Administration',
        description: 'Equip yourself with advanced business strategies and management skills for a dynamic market.',
        duration: '2 Years',
        credits: '15 Credits',
        image: CourseThum3,
        logo:bbaTextShape,
        shape: shape3,
        badge: 'ONLINE'
    }
];

const CourseCard: React.FC<{ course: Course }> = ({ course }) => (
    <div className="col-xl-4 col-lg-6 col-md-6">
        <div className="bd-course-wrapper style-six">
            <Link href="#" className="bd-course-thumb-wrapper bd-course-thumb-style-two p-relative">
                <div className="bd-course-thumb">
                    <Image style={{width:"100%", height:"auto"}} src={course.image} alt={course.title} priority/>
                </div>
                <div className="bd-course-shape">
                    <Image src={course.shape} alt="shape" />
                </div>
                <div className="bd-course-logo">
                    <Image src={course.logo} alt="logo" />
                </div>
                <div className="bd-course-badge">
                    <span className="bd-badge badge-primary">{course.badge}</span>
                </div>
            </Link>
            <div className="bd-course-content">
                <h5 className="bd-course-title underline mb-15">
                    <Link href="#">{course.title}</Link>
                </h5>
                <div className="bd-course-content-body">
                    <p>{course.description}</p>
                </div>
                <div className="bd-course-divider"></div>
                <div className="bd-course-content-bottom d-flex-between flex-wrap gap-15">
                    <div className="bd-course-lesson">
                        <span><i className="fa-light fa-clock"></i> {course.duration}</span>
                    </div>
                    <div className="bd-course-lesson">
                        <span><i className="fa-sharp fa-solid fa-list"></i> {course.credits}</span>
                    </div>
                    <Link className="bd-btn btn-outline-primary" href="#">Apply now</Link>
                </div>
            </div>
        </div>
    </div>
);

const CourseStyleSix: React.FC = () => {
    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 06 Academic</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {courses.map((course, index) => (
                        <CourseCard key={index} course={course} />
                    ))}
                </div>
            </div>
        </section>
    );
};

export default CourseStyleSix;
