"use client"
import React, { useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import JoiningImage from '../../../../../public/assets/images/joining/joining-image.webp';
import JoiningForm from '@/form/JoiningForm';

const JoiningFormArea = () => {
    const [activeStep, setActiveStep] = useState<string>("formStepOne");

    const handleTabClick = (step:string) => {
        setActiveStep(step);
    };

    return (
        <>
            <section className="bd-joining-form-area section-space primary-bg">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6 col-lg-8">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <h2 className="bd-section-title mb-20">Become an Instructor Today</h2>
                                <p className="bd-section-paragraph">Join one of the world’s largest online learning marketplaces. Our Instructor Support Team is ready to help you while our Teaching Center</p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 justify-content-between">
                        <div className="col-xl-5 col-lg-5 col-md-12">
                            <div className="bd-joining-main-thumb">
                                <Image src={JoiningImage} alt="image" />
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-7 col-md-12">
                            <div className="custom-form">
                                {/* -- Stepper -- */}
                                <div className="steps__form mb-20">
                                    <div className="bd-form-setup-panel">
                                        <div className="bd-form-step">
                                            <Link 
                                                href="#formStepOne"
                                                className={`bd-form-step-title ${activeStep === "formStepOne" ? "bd-step-active" : ""}`} 
                                                onClick={() => handleTabClick("formStepOne")}
                                            >
                                                Personal Information
                                            </Link>
                                        </div>
                                        <div className="bd-form-step">
                                            <Link 
                                                href="#formStepTwo"
                                                className={`bd-form-step-title ${activeStep === "formStepTwo" ? "bd-step-active" : ""}`}
                                                onClick={() => handleTabClick("formStepTwo")}
                                            >
                                                Academic Information
                                            </Link>
                                        </div>
                                        <div className="bd-form-step">
                                            <Link 
                                                href="#formStepThree"
                                                className={`bd-form-step-title ${activeStep === "formStepThree" ? "bd-step-active" : ""}`}
                                                onClick={() => handleTabClick("formStepThree")}
                                            >
                                                Confirmation
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                                <JoiningForm activeStep={activeStep} />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default JoiningFormArea;
