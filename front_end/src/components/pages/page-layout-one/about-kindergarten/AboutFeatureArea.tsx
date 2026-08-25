import React from "react";

const featureData = [
    { icon: "fa-solid fa-graduation-cap", title: "Learning & Fun", description: "When it comes to success in the classroom." },
    { icon: "fa-solid fa-utensils", title: "Healthy Meals", description: "Safety matters just as much as the academics." },
    { icon: "fa-solid fa-shield-alt", title: "Children Safety", description: "Consistent with Friend ship’s focus on istudy." },
    { icon: "fa-solid fa-truck", title: "Free Shipping", description: "Guided by teachers who are vested in student success.", extraClass: "first-item" },
];

const AboutFeatureArea = () => {
    return (
        <div className="bd-feature-area p-relative section-space theme-bg-05">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-lg-8 col-md-10">
                        <div className="bd-section-title-wrapper section-title-space text-center">
                            <h2 className="bd-section-title mb-20">Our Core Value</h2>
                            <p className="bd-section-paragraph">
                                View classes by age, program, or subject. Check out <br />
                                upcoming camps and special events too!
                            </p>
                        </div>
                    </div>
                </div>
                <div className="bd-feature-wrapper style-three">
                    <div className="row">
                        <div className="col-12">
                            <ul className="feature">
                                {featureData.map((feature, index) => (
                                    <li className={`feature-item ${feature.extraClass || ""}`} key={index}>
                                        <div className="bd-feature">
                                            <div className="bd-feature-content">
                                                <div className="bd-feature-icon">
                                                    <i className={feature.icon}></i>
                                                </div>
                                                <h4 className="bd-feature-title">{feature.title}</h4>
                                                <p>{feature.description}</p>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AboutFeatureArea;
