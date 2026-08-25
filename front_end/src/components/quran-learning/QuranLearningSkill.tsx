"use client"
import React from "react";
import Image from "next/image";
import SkillThumb from "../../../public/assets/images/skill/skill-thumb-01.webp";
import { useVideoModal } from "@/contextApi/VideoProvider";

const skills = [
    { title: "Tajweed Specialist", percentage: 95 },
    { title: "Hifz Mentor", percentage: 90 },
    { title: "Quranic Scholar", percentage: 80 },
    { title: "Quran Recitation Coach", percentage: 85 },
];

const QuranLearningSkill = () => {
    const { playVideo } = useVideoModal();
    return (
        <>
            {/* -- Skills Area Start -- */}
            <section className="bd-skill-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        {/* Left Column - Skill Content */}
                        <div className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-skill-content-wrapper style-one">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <span className="bd-section-subtitle">Our Skills</span>
                                    <h2 className="bd-section-title mb-20">
                                        Certified Quran Teachers Your Path to Mastery
                                    </h2>
                                    <p className="bd-section-paragraph">
                                        Embark on your journey to Quranic excellence with guidance
                                        from certified Quran teachers. Engage in private sessions
                                        with your instructor for direct feedback.
                                    </p>
                                </div>

                                {/* Skill Progress Bars */}
                                <div className="bd-skill-progress-wrapper">
                                    {skills.map((skill, index) => (
                                        <div className="single-progress" key={index}>
                                            <h6 className="title">{skill.title}</h6>
                                            <div className="progress">
                                                <div
                                                    className="progress-bar wow bdFadeInLeft"
                                                    data-wow-duration="0.5s"
                                                    data-wow-delay=".3s"
                                                    role="progressbar"
                                                    style={{ width: `${skill.percentage}%` }}
                                                    aria-valuenow={skill.percentage}
                                                    aria-valuemin={0}
                                                    aria-valuemax={100}
                                                ></div>
                                                <span className="progress-number">
                                                    {skill.percentage}%
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Right Column - Image & Video */}
                        <div className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-skill-thumb-wrapper style-one">
                                <div className="bd-skill-thumb">
                                    <Image src={SkillThumb} style={{width:"100%", height:"auto"}} alt="Quran Learning Skill" />
                                </div>
                                <div className="bd-skill-video-wrapper text-center">
                                    <div className="bd-skill-video">
                                        <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")}
                                            className="bd-video-btn popup-video has-bg">
                                            <span className="icon">
                                                <i className="fa-solid fa-play"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- Skills Area End -- */}
        </>
    );
};

export default QuranLearningSkill;
