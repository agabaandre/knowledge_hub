import Image, { StaticImageData } from 'next/image';
import Link from 'next/link';
import React from 'react';
import cscTextShape from '../../../../public/assets/images/course/csc.webp';
import bapTextShape from '../../../../public/assets/images/course/bap.webp';
import bbaTextShape from '../../../../public/assets/images/course/bba.webp';
import courseShape from "../../../../public/assets/images/course/course-shape-4.webp";
import courseShape2 from "../../../../public/assets/images/course/course-shape-5.webp";
import courseShape3 from "../../../../public/assets/images/course/course-shape-3.webp";
//avatar
import authorAvatar from "../../../../public/assets/images/avatar/avatar.webp";
import authorAvatar2 from "../../../../public/assets/images/avatar/avatar2.webp";
import authorAvatar3 from "../../../../public/assets/images/avatar/avatar3.webp";
//thumb image
import courseThumb1 from "../../../../public/assets/images/course/course-thumb-4.webp";
import courseThumb2 from "../../../../public/assets/images/course/course-thumb-5.webp";
import courseThumb3 from "../../../../public/assets/images/course/course-thumb-6.webp";

interface ICourse {
    image: StaticImageData,
    shape: StaticImageData,
    badge: string,
    title: string,
    authorImage: StaticImageData,
    authorName: string,
    description: string,
    duration: string,
    credits: string,
    category: string,
    logo: StaticImageData
}

const courses: ICourse[] = [
    {
        image: courseThumb1,
        shape: courseShape,
        badge: "FULL-TIME",
        title: "Master of Science in Data Science",
        authorImage: authorAvatar,
        authorName: "Emma Clark",
        description: "Master statistical methods, machine learning, and big data analysis to uncover insights in data-driven fields.",
        duration: "2 Years",
        credits: "16",
        category: "cat2",
        logo: cscTextShape
    },
    {
        image: courseThumb2,
        shape: courseShape2,
        badge: "PART-TIME",
        title: "Master of Public Health (MPH)",
        authorImage: authorAvatar2,
        authorName: "Emma Clark",
        description: "Develop skills to address global health challenges and improve community well-being through public health strategies.",
        duration: "2 Years",
        credits: "14",
        category: "cat2",
        logo: bapTextShape
    },
    {
        image: courseThumb3,
        shape: courseShape3,
        badge: "RESEARCH",
        title: "PhD in Artificial Intelligence",
        authorImage: authorAvatar3,
        authorName: "Emma Clark",
        description: "Conduct groundbreaking research in AI, machine learning, and deep learning to push the boundaries of technology and innovation.",
        duration: "5 Years",
        credits: "20",
        category: "cat3",
        logo: bbaTextShape
    }
];

const CourseStyleSeven = () => {
    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 07 with Author</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {courses.map((course, index) => (
                        <div className={`col-xl-4 col-lg-6 col-md-6 grid-item ${course.category}`} key={index}>
                            <div className="bd-course-wrapper style-six">
                                <Link href="#" className="bd-course-thumb-wrapper bd-course-thumb-style-two p-relative">
                                    <div className="bd-course-thumb">
                                        <Image style={{width:"100%", height:"auto"}} src={course.image} alt="course" />
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
                                        <div className="bd-course-author mb-15">
                                            <div className="thumb">
                                                <Image src={course.authorImage} alt="author" />
                                            </div>
                                            <div className="name">
                                                <Link href="#">{course.authorName}</Link>
                                            </div>
                                        </div>
                                        <p>{course.description}</p>
                                    </div>
                                    <div className="bd-course-divider"></div>
                                    <div className="bd-course-content-bottom d-flex-between flex-wrap gap-15">
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-light fa-clock"></i> {course.duration}</span>
                                        </div>
                                        <div className="bd-course-lesson">
                                            <span><i className="fa-sharp fa-solid fa-list"></i> {course.credits} Credits</span>
                                        </div>
                                        <Link className="bd-btn btn-outline-primary" href="#">Apply now</Link>
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

export default CourseStyleSeven;
