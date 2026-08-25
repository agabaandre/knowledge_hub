"use client"
import Breadcrumbs from "@/components/common/Breadcrumb/Breadcrumbs";
import { pricingPlans } from "@/data/pricing-plan-data";
import React, { useState } from "react";
import PricingPlanCard from "./PricingPlanCard";
import AboutCtaArea from "@/components/common/about-cta/AboutCtaArea";

// Main Pricing Area component
const PricingArea: React.FC = () => {
    // State to toggle between Yearly and Monthly pricing
    const [isYearly, setIsYearly] = useState(false);

    return (
        <>
            <Breadcrumbs breadcrumbTitle='Pricing Table' />
            <section className="bd-faq-area section-space-top p-relative">
                <div className="container">
                    <div className="row g-30 align-items-center justify-content-between section-title-space">
                        <div className="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Affordable Plans</span>
                                <h2 className="bd-section-title">Choose Your Ideal Plan</h2>
                            </div>
                        </div>
                        <div className="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div className="text-start text-md-end">
                                <div className="bd-pricing-plan-btn-group">
                                    <button
                                        className={`yearly-plan-btn ${isYearly ? "active" : ""}`}
                                        type="button"
                                        onClick={() => setIsYearly(true)}
                                    >
                                        Yearly
                                    </button>
                                    <button
                                        className={`monthly-plan-btn ${!isYearly ? "active" : ""}`}
                                        type="button"
                                        onClick={() => setIsYearly(false)}
                                    >
                                        Monthly
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {pricingPlans.map((plan) => (
                            <PricingPlanCard key={plan.id} plan={plan} isYearly={isYearly} />
                        ))}
                    </div>
                </div>
            </section>
            <AboutCtaArea aboutWrapper={true}/>
        </>
    );
};

export default PricingArea;
