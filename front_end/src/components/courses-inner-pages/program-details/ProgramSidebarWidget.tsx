import Link from 'next/link';
import React from 'react';
import avatarImg from "../../../../public/assets/images/avatar/avatar.webp";
import programFaqImg from "../../../../public/assets/images/shape/progrum-faq.svg";
import Image from 'next/image';

const ProgramSidebarWidget = () => {
    return (
        <>
            <div className="bd-program-sidebar sidebar-right sidebar-sticky">
                <div className="bd-program-sidebar-widget mb-30">
                    <div className="bd-program-sidebar-link">
                        <Link className="bd-btn btn-primary w-100" href="/faculty-members">Faculty & Staff</Link>
                    </div>
                    <div className="bd-program-sidebar-link">
                        <Link className="bd-btn btn-secondary w-100" href="/scholarships-financial-aid">Scholarships &
                            Financial Aid</Link>
                    </div>
                    <div className="bd-program-sidebar-link">
                        <Link className="bd-btn btn-danger w-100" href="/apply-online">Apply Online</Link>
                    </div>
                </div>
                <div className="bd-program-sidebar-widget mb-30">
                    <div className="bd-instructor-wrapper style-two">
                        <div className="bd-instructor-item">
                            <div className="bd-instructor-thumb-wrap">
                                <div className="bd-instructor-thumb">
                                    <Image style={{width:"100%", height:"auto"}} src={avatarImg} alt="image" />
                                </div>
                            </div>
                            <div className="bd-instructor-info">
                                <h6 className="name underline"><Link href="/instructor/instructor-details">Dr. Alex Johnson</Link></h6>
                                <span className="designation">Associate Professor</span>
                                <span className="designation">Department Head</span>
                                <span className="institute">Ph.D., Stanford University, USA</span>
                                <span className="email"><strong>Email:</strong> alex.johnson@northsouth.edu</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-program-sidebar-widget">
                    <div className="bd-program-sidebar-faq">
                        <h4 className="title mb-10">Do you have more questions?</h4>
                        <p className="description underline">Read our <Link href="/faq">FAq</Link> or <Link href="/contact">Contact Us</Link></p>
                        <div className="thumb"> <Image style={{width:"100%", height:"auto"}} src={programFaqImg} alt="image" /></div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ProgramSidebarWidget;