
import { coursePricingPlanData } from '@/data/pricing-plan-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const CoursePricingPlanArea = () => {
    return (
        <>
            {/* -- pricing plan area start -- */}
            <section className="bd-pricing-area section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xxl-6 col-xl-8">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Choose Your Perfect Plan</span>
                                <h2 className="bd-section-title">
                                    Flexible <span className="down-mark-line">Learning</span> Options
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {coursePricingPlanData.map((plan) => (
                            <div key={plan.id} className="col-lg-4 col-md-6">
                                <div className={`bd-pricing-plan-wrap style-four ${plan.hasBackground ? 'has-bg' : ''}`}>
                                    <div className="bd-pricing-item">
                                        <div className="bd-pricing-thumb">
                                            <Image src={plan.image} style={{ width: "100%", height: "auto" }} alt="pricing" />
                                            <h3 className="title">
                                                <Link href={plan.url}>{plan.title}</Link>
                                            </h3>
                                            <div className="pricing-course">
                                                <h4 className="subtitle">{plan.courses} <span>Courses</span></h4>
                                            </div>
                                        </div>
                                        <div className="bd-pricing-content">
                                            <p>{plan.description}</p>
                                            <ul className="bd-pricing-plan-list-3">
                                                {plan.features.map((feature, index) => (
                                                    <li key={index}>
                                                        <Image src={plan.checkIcon} style={{ width: "100%", height: "auto" }} alt="icon" /> {feature}
                                                    </li>
                                                ))}
                                            </ul>
                                            <div className="price-wrap">
                                                <span>Start From</span>
                                                <h3 className="price">
                                                    ${plan.price.toFixed(2)}
                                                    <sub>/Month</sub>
                                                </h3>
                                            </div>
                                            <div className="pricing-btn">
                                                <Link className="bd-text-btn" href={plan.url}>
                                                    View Plans
                                                    <div className="box-icon">
                                                        <span className="first-icon">
                                                            <i className="fa-regular fa-angle-right"></i>
                                                        </span>
                                                        <span className="second-icon">
                                                            <i className="fa-regular fa-angle-right"></i>
                                                        </span>
                                                    </div>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- pricing plan area end -- */}
        </>
    );
};

export default CoursePricingPlanArea;



