import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import breadcrumbBg from '../../../../../public/assets/images/breadcrumb/breadcrumb-bg-kin.webp';
import breadcrumbShape from '../../../../../public/assets/images/shape/breadcrumb-shape-1.webp';
import bulbShape from '../../../../../public/assets/images/shape/breadcrumb-shape-2.webp';

const AboutBreadcrumb = () => {
    return (
        <>
            {/* -- breadcrumb area start -- */}
            <section className="bd-breadcrumb-area p-relative fix z-index-11">
                <div className="bd-breadcrumb-bg style-three" style={{ backgroundImage: `url(${breadcrumbBg.src})` }}></div>
                <div className="bd-breadcrumb-wrapper p-relative">
                    <div className="container">
                        <div className="row">
                            <div className="col-xl-12">
                                <div className="bd-breadcrumb d-flex-start">
                                    <div className="bd-breadcrumb-content">
                                        <h1 className="bd-breadcrumb-title">About Kindergarten</h1>
                                        <div className="bd-breadcrumb-list has-white justify-content-start">
                                            <span><Link href="/">iStudy</Link></span>
                                            <span className="divider"><i className="fa-regular fa-angle-right"></i></span>
                                            <span className="active">About Kindergarten</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bd-breadcrumb-shape">
                            <div className="shape-1"><Image style={{ width: "100%", height: "auto" }} src={breadcrumbShape} alt="shape" /></div>
                            <div className="shape-3"><Image style={{ width: "100%", height: "auto" }} src={bulbShape} alt="shape" /></div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- breadcrumb area end -- */}
        </>
    );
};

export default AboutBreadcrumb;