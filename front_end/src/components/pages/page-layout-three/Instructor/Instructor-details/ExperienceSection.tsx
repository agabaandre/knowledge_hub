import BookSvg from '@/svg/BookSvg';
import RatingSvg from '@/svg/RatingSvg';
import StudentsSvg from '@/svg/StudentsSvg';
import React from 'react';

const ExperienceSection = () => {
    return (
        <>
            <div className="bd-instructor-feature-box mb-30">
                <h3 className="bd-instructor-details-title">Experience</h3>
                <p className="bd-instructor-details-desc">Alex has been a Senior Data Scientist at TechCorp,
                    where he worked on various
                    projects related to predictive analytics, machine learning algorithms, and big data
                    processing. His courses are
                    well-known for their hands-on approach and industry relevance.</p>
                <div className="bd-experience-box-wrapper">
                    <div className="bd-experience-box">
                        <div className="icon">
                         <BookSvg/>
                        </div>
                        <h3 className="title">25</h3>
                        <p className="subtitle">Total Courses</p>
                    </div>
                    <div className="bd-experience-box">
                        <div className="icon">
                           <StudentsSvg/>
                        </div>
                        <h3 className="title">15k</h3>
                        <p className="subtitle">Total Students</p>
                    </div>
                    <div className="bd-experience-box">
                        <div className="icon">
                            <RatingSvg/>
                        </div>
                        <h3 className="title">4.8</h3>
                        <p className="subtitle">Average Rating</p>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ExperienceSection;