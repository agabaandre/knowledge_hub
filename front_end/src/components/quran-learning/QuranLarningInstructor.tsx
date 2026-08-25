import instructorsData from '@/data/instructor-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const QuranLarningInstructor = () => {
    return (
        <>
            {/* -- instructor area start -- */}
            <section className="bd-instructor-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="bd-section-title-wrapper section-title-space text-center">
                            <span className="bd-section-subtitle">Mentors</span>
                            <h2 className="bd-section-title">Meet Our Instructor</h2>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {
                            instructorsData.slice(30, 34).map((item) => (
                                <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6" key={item.id}>
                                    <div className="bd-instructor-wrapper style-ten">
                                        <div className="bd-instructor-item">
                                            <div className="bd-instructor-thumb-wrap">
                                                <div className="bd-instructor-thumb">
                                                    <Link href={`/instructor/instructor-details/${item.id}`}><Image src={item.image} alt="image" /></Link>
                                                </div>
                                                <div className="bd-instructor-social theme-social has-white circle text-center">
                                                    <ul className="social-icon-list">
                                                        <li className="bd-icon-1">
                                                            <Link href={item.socialLinks?.facebook || '#'}><i className="fa-brands fa-facebook-f"></i></Link>
                                                        </li>
                                                        <li className="bd-icon-2"><Link href={item.socialLinks?.twitter || '#'}><i className="fa-brands fa-x-twitter"></i></Link></li>
                                                        <li className="bd-icon-3"><Link href={item.socialLinks?.linkedin || '#'}><i className="fa-brands fa-linkedin-in"></i></Link></li>
                                                        <li className="bd-icon-4"><Link href={item.socialLinks?.instagram || '#'}><i className="fa-brands fa-instagram"></i></Link></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div className="bd-instructor-info">
                                                <h6 className="name underline"><Link href={`/instructor/instructor-details/${item.id}`}>{item.name}</Link>
                                                </h6>
                                                <span>{item.title}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
            {/* -- instructor area start -- */}
        </>
    );
};

export default QuranLarningInstructor;