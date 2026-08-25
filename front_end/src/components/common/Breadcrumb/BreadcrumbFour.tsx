import Link from 'next/link';
import React from 'react';
import breadcrumbBg from '../../../../public/assets/images/breadcrumb/breadcrumb-bg-2.webp';
import breadcrumbShape from '../../../../public/assets/images/shape/breadcrumb-shape-1.webp';
import bulbShape from '../../../../public/assets/images/shape/bulb-shape.webp';
import Image from 'next/image';
interface breadCrumbType {
    title: string
    subTitle: string
}

const BreadcrumbFour = ({ title, subTitle }: breadCrumbType) => {
    return (
        <>
            <section className="bd-breadcrumb-area p-relative fix z-index-11">
                <div className="bd-breadcrumb-bg-two" style={{ backgroundImage: `url(${breadcrumbBg.src})` }}></div>
                <div className="bd-breadcrumb-wrapper p-relative">
                    <div className="container">
                        <div className="row">
                            <div className="col-xl-12">
                                <div className="bd-breadcrumb style-two d-flex-center justify-content-start">
                                    <div className="bd-breadcrumb-content">
                                        <h1 className="bd-breadcrumb-title">{title}</h1>
                                        <div className="bd-breadcrumb-list justify-content-start">
                                            <span><Link href="/">iStudy</Link></span>
                                            <span className="divider"><i className="fa-regular fa-angle-right"></i></span>
                                            <span className="active">{subTitle}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bd-breadcrumb-shape">
                            <div className="shape-1"><Image src={breadcrumbShape} alt="shape" /></div>
                            <div className="shape-3"><Image src={bulbShape} alt="shape" /></div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default BreadcrumbFour;