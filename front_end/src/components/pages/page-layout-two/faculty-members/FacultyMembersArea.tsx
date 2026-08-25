import Image from 'next/image';
import React from 'react';
import Link from 'next/link';
import { executiveLeadersData } from '@/data/executive-leaders-data';

const FacultyMembersArea = () => {
    return (
        <>
            <section className="bd-executive-leaders-area section-space">
                <div className="container">
                    <div className="row g-30 justify-content-center">
                        {
                            executiveLeadersData.slice(2, 8).map((item, index) =>
                                <div className="col-xxl-6 col-xl-6 col-lg-4 col-md-12" key={index}>
                                    <div className="bd-instructor-wrapper style-eight">
                                        <div className="bd-instructor-thumb">
                                            <Link href="/instructor/instructor-details"><Image src={item.image} style={{ width: '100%', height: 'auto' }} alt="image" /></Link>
                                        </div>
                                        <div className="bd-instructor-info">
                                            <h6 className="name underline"><Link href="/instructor/instructor-details">{item.name}</Link></h6>
                                            <span className="designation">{item.designation}</span>
                                            <span className="institute">{item.instituteOne}</span>
                                            <span className="institute">{item.instituteTwo}</span>
                                            <span className="email"><strong>Email: </strong> {item.email}</span>
                                        </div>
                                    </div>
                                </div>
                            )
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default FacultyMembersArea;