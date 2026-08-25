import React from 'react';

const WhatYouLearnTwo = () => {
    return (
        <>
            <div className="bd-course-feature-box mt-20" id="details">
                <h3 className="bd-course-details-content-title">What {`you'll`} learn</h3>
                <div className="row gy-30">
                    <div className="col-lg-6">
                        <div className="bd-course-details-list">
                            <ul>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Master the principles of design: typography, layout, and color theory
                                </li>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Create visually appealing compositions using design tools
                                </li>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Develop branding and visual identity for businesses and personal projects
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="bd-course-details-list">
                            <ul>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Utilize industry-standard tools like Adobe Photoshop and Illustrator
                                </li>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Build professional-grade portfolios with real-world projects
                                </li>
                                <li>
                                    <span className="list-icon success">
                                        <i className="fa-solid fa-check"></i>
                                    </span>
                                    Understand the impact of color and typography in design psychology
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default WhatYouLearnTwo;