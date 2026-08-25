import React from 'react';
import FacultyDirectorySvg from '@/svg/banner/FacultyDirectorySvg';
import ScholarshipFinancialSvg from '@/svg/banner/ScholarshipFinancialSvg';
import InteractiveCourseSvg from '@/svg/banner/InteractiveCourseSvg';

// Define types for the FeatureItem props
interface FeatureItemProps {
    IconComponent: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    title: string;
    isActive?: boolean;
}

const FeatureItem: React.FC<FeatureItemProps> = ({ IconComponent, title, isActive = false }) => (
    <div className={`bd-feature-item ${isActive ? 'has-active' : ''}`}>
        <span className="bd-feature-icon">
            <IconComponent />
        </span>
        <div className="bd-feature-content">
            <h5 className="bd-feature-title">{title}</h5>
        </div>
    </div>
);

const UniversityFeatureSection: React.FC = () => {
    return (
        <div className="bd-feature-area">
            <div className="container">
                <div className="bd-feature-wrapper style-two">
                    <FeatureItem IconComponent={InteractiveCourseSvg} title="Interactive Course" />
                    <FeatureItem IconComponent={FacultyDirectorySvg} title="Faculty Directory" isActive />
                    <FeatureItem IconComponent={ScholarshipFinancialSvg} title="Scholarship & Financial" />
                </div>
            </div>
        </div>
    );
};

export default UniversityFeatureSection;
