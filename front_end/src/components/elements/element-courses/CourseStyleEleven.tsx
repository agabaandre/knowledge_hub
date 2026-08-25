import React from 'react';
import Image, { StaticImageData } from 'next/image';
import courseImg1 from '../../../../public/assets/images/course/course-img-8.webp';
import courseImg2 from '../../../../public/assets/images/course/course-img-7.webp';
import courseImg3 from '../../../../public/assets/images/course/course-img-9.webp';
import Link from 'next/link';

interface Course {
    id: number;
    image: StaticImageData;
    price: string;
    discount: string;
    title: string;
    description: string;
    lessons: string;
    students: string;
}

const courses: Course[] = [
    {
        id: 1,
        image: courseImg1,
        price: "$35",
        discount: "15% Off",
        title: "Bachelor of Business Administration (Accounting)",
        description: "Helping employees gain skills and development often take a back seat to business",
        lessons: "15 Lessons",
        students: "313 Students"
    },
    {
        id: 2,
        image: courseImg2,
        price: "$35",
        discount: "15% Off",
        title: "Bachelor of Business Administration (Accounting)",
        description: "Helping employees gain skills and development often take a back seat to business",
        lessons: "15 Lessons",
        students: "313 Students"
    },
    {
        id: 3,
        image: courseImg3,
        price: "$35",
        discount: "15% Off",
        title: "Bachelor of Business Administration (Accounting)",
        description: "Helping employees gain skills and development often take a back seat to business",
        lessons: "15 Lessons",
        students: "313 Students"
    }
];

const CourseCard: React.FC<Course> = ({ image, price, discount, title, description, lessons, students }) => (
    <div className="col-xl-4 col-lg-6 col-md-6">
        <div className="bd-course-wrapper style-nine primary-bg">
            <div className="bd-course-thumb-wrapper p-relative">
                <div className="bd-course-thumb">
                    <Link href="#">
                        <Image style={{width:"100%", height:"auto"}} src={image} alt={title} layout="responsive" width={300} height={200} />
                    </Link>
                </div>
                <div className="bd-course-badge">
                    <span className="bd-circle-badge white">{price}</span>
                </div>
            </div>
            <div className="bd-course-content">
                <Link className="bd-badge badge-warning mb-10" href="#">{discount}</Link>
                <h5 className="bd-course-title underline mb-10">
                    <Link href="#">{title}</Link>
                </h5>
                <p className="bd-course-description">{description}</p>
                <div className="bd-course-content-bottom d-flex align-items-center">
                    <div className="bd-course-lesson has-separator">
                        <span><i className="fa-light fa-clock"></i> {lessons}</span>
                    </div>
                    <div className="bd-course-lesson">
                        <span><i className="fa-sharp fa-solid fa-list"></i> {students}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
);

const CourseStyleEleven: React.FC = () => {
    return (
        <section className="bd-elements-course section-space-bottom">
            <div className="container">
                <div className="row align-items-center justify-content-center">
                    <div className="col-lg-12">
                        <div className="bd-elements-section-wrapper section-title-space text-center">
                            <div className="bd-elements-line">
                                <div className="bd-separator-line line-left"></div>
                                <div className="bd-elements-title-wrapper">
                                    <h2 className="bd-elements-title small">Course Style 11</h2>
                                </div>
                                <div className="bd-separator-line line-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {courses.map(course => <CourseCard key={course.id} {...course} />)}
                </div>
            </div>
        </section>
    );
};

export default CourseStyleEleven;