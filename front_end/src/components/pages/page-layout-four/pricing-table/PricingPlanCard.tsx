import { PricingPlanCardProps } from "@/interFace/pricing-interface";
import Image from "next/image";
import Link from "next/link";


const PricingPlanCard: React.FC<PricingPlanCardProps> = ({ plan, isYearly }) => {
    const { badge, thumb, alt, title, description, yearly, monthly, buttonText, buttonType, features } = plan;
    const activePrice = isYearly ? yearly : monthly;

    return (
        <div className="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">
            <div className="bd-pricing-plan-wrap style-two">
                <div className="bd-pricing-plan-card">
                    <div className="pricing-header">
                        {badge && (
                            <div className="pricing-badge">
                                <span>{badge}</span>
                            </div>
                        )}
                        <div className="thumb">
                            <Image src={thumb} alt={alt} />
                        </div>
                        <h3 className="title">{title}</h3>
                        <p className="description">{description}</p>
                        <div className="price-wrap">
                            <div
                                className="common-price yearly-pricing"
                                style={{ display: isYearly ? "block" : "none" }}
                            >
                                <span className="dollar">$</span>
                                <h2 className="amount">{yearly.price}</h2>
                                <span className="duration">{yearly.duration}</span>
                            </div>
                            <div
                                className="common-price monthly-pricing"
                                style={{ display: !isYearly ? "block" : "none" }}
                            >
                                <span className="dollar">$</span>
                                <h2 className="amount">{activePrice.price}</h2>
                                <span className="duration">{activePrice.duration}</span>
                            </div>
                        </div>
                    </div>
                    <div className="pricing-btn">
                        <Link
                            className={`bd-btn btn-${buttonType} w-100`}
                            href="#"
                        >
                            {buttonText}
                        </Link>
                    </div>
                    <div className="pricing-body">
                        <ul className="bd-pricing-plan-list">
                            {features.map((feature, idx) => (
                                <li key={idx}>
                                    <i className="fa-solid fa-check"></i> {feature}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PricingPlanCard;