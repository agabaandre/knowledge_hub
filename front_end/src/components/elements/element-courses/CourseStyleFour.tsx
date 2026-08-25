import Image, { StaticImageData } from "next/image";
import React from "react";
import courseShape from "../../../../public/assets/images/shape/course-shape.webp";
import homeProgram1 from "../../../../public/assets/images/program/home-program-1.webp";
import homeProgram2 from "../../../../public/assets/images/program/home-program-2.webp";
import homeProgram3 from "../../../../public/assets/images/program/home-program-3.webp";
import Link from "next/link";

interface Course {
    id: number;
    title: string;
    description: string;
    image: StaticImageData;
    lessons: number;
    students: string;
    hours: number;
    bgClass: string;
}

const courses: Course[] = [
    {
        id: 1,
        title: "To round out our weekend of celebrations",
        description: "To round out our weekend of celebrations, we are holding our reunion.",
        image: homeProgram1,
        lessons: 25,
        students: "150+",
        hours: 50,
        bgClass: "theme-bg",
    },
    {
        id: 2,
        title: "To round out our weekend of celebrations",
        description: "To round out our weekend of celebrations, we are holding our reunion.",
        image: homeProgram2,
        lessons: 25,
        students: "150+",
        hours: 50,
        bgClass: "warning-bg",
    },
    {
        id: 3,
        title: "To round out our weekend of celebrations",
        description: "To round out our weekend of celebrations, we are holding our reunion.",
        image: homeProgram3,
        lessons: 25,
        students: "150+",
        hours: 50,
        bgClass: "info-bg",
    },
];

const CourseStyleFour: React.FC = () => {
    return (
        <section className="bd-elements-course section-space primary-bg">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 04</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {courses.map(({ id, title, description, image, lessons, students, hours, bgClass }) => (
                        <div key={id} className="col-xl-4 col-lg-4 col-md-6">
                            <div className="bd-course-wrapper style-four">
                                <div className="bd-course-thumb-wrapper p-relative mb-20">
                                    <Link href="#">
                                        <div className="bd-course-thumb">
                                            <Image style={{width:"100%", height:"auto"}} src={image} alt="course" />
                                        </div>
                                        <div className="shape">
                                            <Image src={courseShape} alt="shape" />
                                        </div>
                                    </Link>
                                </div>
                                <div className="bd-course-content mb-20">
                                    <h5 className="bd-course-title underline mb--5">
                                        <Link href="#">{title}</Link>
                                    </h5>
                                    <p>{description}</p>
                                </div>
                                <div className={`bd-course-info-item-wrapper ${bgClass}`}>
                                    <div className="bd-course-info-item">
                                        <h5 className="bd-course-info-item-title">
                                            {lessons}
                                            <br />
                                            <span>Lessons</span>
                                        </h5>
                                    </div>
                                    <div className="bd-course-info-item">
                                        <h5 className="bd-course-info-item-title">
                                            {students}
                                            <br />
                                            <span>Students</span>
                                        </h5>
                                    </div>
                                    <div className="bd-course-info-item">
                                        <h5 className="bd-course-info-item-title">
                                            {hours}
                                            <br />
                                            <span>Hours</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default CourseStyleFour;
