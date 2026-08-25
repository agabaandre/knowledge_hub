import React from 'react';

const CourseCommonDetailsContent = () => {
    return (
        <>
            <div className="bd-course-feature-box mt-20" id="overview">
                <h3 className="bd-course-details-content-title">Course Details</h3>
                <p className="description mb-15">
                    This course covers the foundational principles of graphic design, including typography,
                    layout, color theory,
                    and branding. Ideal for beginners and those looking to refine their design skills with
                    real-world projects.
                </p>
                <div className="row gx-30 gy-10">
                    <div className="col-lg-6">
                        <div className="bd-post-details-list">
                            <ul>
                                <li>Introduction to Graphic Design</li>
                                <li>Basics of Typography</li>
                                <li>Color Theory and Applications</li>
                            </ul>
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="bd-post-details-list">
                            <ul>
                                <li>Layout and Composition Techniques</li>
                                <li>Branding and Visual Identity</li>
                                <li>Real-World Design Projects</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default CourseCommonDetailsContent;