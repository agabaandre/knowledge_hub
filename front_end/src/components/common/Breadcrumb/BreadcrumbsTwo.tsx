import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import breadcrumbShapeOne from '../../../../public/assets/images/shape/breadcrumb-shape-1.webp';
import breadcrumbShapeTwo from '../../../../public/assets/images/shape/breadcrumb-shape-2.webp';
import breadcrumbBg from '../../../../public/assets/images/breadcrumb/breadcrumb-bg.webp'

interface breadCrumbType{
    breadcrumbTwoTitle:string
}
const BreadcrumbsTwo = ({breadcrumbTwoTitle}:breadCrumbType) => {
    return (
        <>
            <section className="bd-breadcrumb-area p-relative fix z-index-11">
                <div className="bd-breadcrumb-bg" style={{backgroundImage:`url(${breadcrumbBg.src})`}}></div>
                <div className="bd-breadcrumb-wrapper p-relative">
                    <div className="container">
                        <div className="row">
                            <div className="col-xl-12">
                                <div className="bd-breadcrumb d-flex-start">
                                    <div className="bd-breadcrumb-content">
                                        <h1 className="bd-breadcrumb-title">{breadcrumbTwoTitle}</h1>
                                        <div className="bd-breadcrumb-list has-white justify-content-start">
                                            <span><Link href="/">IStudy</Link></span>
                                            <span className="divider"><i className="fa-regular fa-angle-right"></i></span>
                                            <span className="active">{breadcrumbTwoTitle}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bd-breadcrumb-shape">
                            <div className="shape-1"><Image src={breadcrumbShapeOne} alt="shape"/></div>
                            <div className="shape-2"><Image src={breadcrumbShapeTwo} alt="shape"/></div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default BreadcrumbsTwo;