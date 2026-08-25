import Link from 'next/link';
import React from 'react';

const CampusAboutArea = () => {
    return (
        <>
            <section  className="bd-campus-story section-space">
                <div  className="container">
                    <div  className="row justify-content-center">
                        <div  className="col-xl-6">
                            <div  className="bd-section-title-wrapper section-title-space text-center">
                                <h2  className="bd-section-title">Our Campus Journey</h2>
                            </div>
                        </div>
                    </div>
                    <div  className="row gy-30 justify-content-center">
                        <div  className="col-lg-12">
                            <div  className="bd-campus-story-content text-center">
                                <p  className="bd-campus-story-highlight">Discover how iStudy University has grown into a
                                    vibrant hub of innovation and learning, inspiring thousands of students to reach new
                                    heights in their educational journey.</p>

                                <div  className="bd-campus-story-btn"><Link  className="bd-btn btn-primary" href="/about-university">Learn More About iStudy</Link></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default CampusAboutArea;