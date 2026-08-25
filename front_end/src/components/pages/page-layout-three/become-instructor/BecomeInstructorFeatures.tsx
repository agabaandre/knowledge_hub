import ControlContentSvg from '@/svg/ControlContentSvg';
import MonetizeKnowledgeSvg from '@/svg/MonetizeKnowledgeSvg';
import NetworkSvg from '@/svg/NetworkSvg';
import React from 'react';
// Define the IFeature interface
interface IFeature {
    icon: React.FC;
    title: string;
    description: string;
}

// Features array with icon, title, and description for each feature
const features: IFeature[] = [
    {
        icon: NetworkSvg,
        title: 'Expand Your Network',
        description: 'Connect with other professionals, build your expertise, and earn money with each paid enrollment.'
    },
    {
        icon: ControlContentSvg,
        title: 'Control Your Content',
        description: 'Publish courses on your terms and maintain control over your own content. Share your passion with the world.'
    },
    {
        icon: MonetizeKnowledgeSvg,
        title: 'Monetize Your Knowledge',
        description: 'Earn from your expertise. Our platform supports various career-oriented courses to help you succeed.'
    }
];
const BecomeInstructorFeatures = () => {

    return (
        <>
        {/* Joining Features Section */}
        <section className="bd-joining-features-area section-space">
                <div className="container">
                    <div className="row g-30">
                        <div className="col-12">
                            <div className="section-title text-center mb-50">
                                <h2>Empower Others Through Teaching</h2>
                            </div>
                        </div>
                        {features.map((feature, index) => (
                            <div key={index} className="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                                <div className="bd-joining-features-box text-center">
                                    <div className="bd-joining-features-icon"><feature.icon /></div>
                                    <div className="bd-joining-features-content">
                                        <h4 className="title">{feature.title}</h4>
                                        <p className="description">{feature.description}</p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
};

export default BecomeInstructorFeatures;