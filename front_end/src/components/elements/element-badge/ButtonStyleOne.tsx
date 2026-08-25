import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import AvatarImg from '../../../../public/assets/images/avatar/avatar.webp';

const ButtonStyleOne = () => {
    return (
        <>
            {/* -- button style 01 start -- */}
            <section className="bd-elements-button section-space">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Badge Primary Style</h2>
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
                                <div className=""><Link className="bd-badge badge-primary" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-secondary" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-success" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-warning" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-danger" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-info" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-light" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-dark" href="#">istudy Badge</Link></div>
                                <div className=""><Link className="bd-badge badge-primary" href="#"><Image src={AvatarImg} alt="images" /> istudy Badge</Link></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- button style 01 end -- */}
        </>
    );
};

export default ButtonStyleOne;