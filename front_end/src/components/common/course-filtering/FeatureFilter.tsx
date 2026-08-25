import { IFeatureFilter } from '@/interFace/interFace';
import React from 'react';

 interface SidebarFeaturesProps {
  features: IFeatureFilter[];
  onFeatureChange?: (id: string, isChecked: boolean) => void;
}

const SidebarFeatures: React.FC<SidebarFeaturesProps> = ({ features, onFeatureChange }) => {
  const handleCheckboxChange = (id: string, isChecked: boolean) => {
    if (onFeatureChange) {
      onFeatureChange(id, isChecked);
    }
  };

  return (
    <div className="bd-course-widget widget-features">
      <h5 className="bd-widget-title mb-20">Features</h5>
      <div className="bd-widget-content">
        {features.map((feature) => (
          <div className="checkbox-option" key={feature.id}>
            <input
              id={`course-feature-${feature.id}`}
              type="checkbox"
              onChange={(e) => handleCheckboxChange(feature.id, e.target.checked)}
            />
            <label htmlFor={`course-feature-${feature.id}`}>
              {feature.name} <span>({feature.count})</span>
            </label>
          </div>
        ))}
      </div>
    </div>
  );
};

export default SidebarFeatures;
