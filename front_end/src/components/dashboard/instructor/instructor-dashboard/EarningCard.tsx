import InstructorDashboardCounter from "@/components/common/counter/InstructorDashboardCounter";
import { EarningData } from "@/interFace/dashboard-interface";


// EarningCard component
const EarningCard: React.FC<EarningData> = ({ icon, amount, label, suffix }) => {
    const symbol = suffix === "$" ? "$" : suffix;
    return (
        <div className="col-xl-4 col-lg-6 col-md-12 col-sm-6">
            <div className="bd-counter-wrapper bd-counter-style-six">
                <div className="bd-counter-item">
                    <div className="bd-counter-content">
                        <span className="bd-counter-icon bg-two">
                            <i className={`fa-solid ${icon}`}></i>
                        </span>
                        <h2 className="bd-counter-title">
                            <InstructorDashboardCounter countNum={amount} symbol={symbol} />
                        </h2>
                        <p>{label}</p>
                    </div>
                </div>
            </div>
        </div>
    );
};
export default EarningCard;