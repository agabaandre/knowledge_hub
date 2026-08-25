import Link from 'next/link';
import React from 'react';
import AvatarImg from '../../../../public/assets/images/avatar/avatar.webp';
import Image from 'next/image';

const ButtonStyleTwo = () => {
    return (
        <>
            {/* -- button style 02 start -- */}
            <section className="bd-elements-button section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Badge Outline Style</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row justify-content-center justify-content-center">
                        <div className="col-xl-12">
                            <div className="d-flex flex-wrap justify-content-between align-items-center gap-30">
                                <div className=""><Link className="bd-badge badge-outline-primary badge-transparent radius-0" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-secondary badge-transparent radius-6" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-success badge-transparent radius-8" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-warning badge-transparent radius-10" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-danger badge-transparent" href="#">istudy Badge</Link>
                                </div>
                                <div className=""><Link className="bd-badge badge-outline-info badge-transparent radius-16" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-light badge-transparent radius-24" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-dark badge-transparent radius-50" href="#">istudy
                                    Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-outline-primary badge-transparent radius-50" href="#"><Image src={AvatarImg} alt="images" /> istudy Badge</Link></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- button style 02 end -- */}
        </>
    );
};

export default ButtonStyleTwo;