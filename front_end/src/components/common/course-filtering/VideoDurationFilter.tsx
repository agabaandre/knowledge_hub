import { IVideoDuration } from '@/interFace/interFace';
import React from 'react';

 interface SidebarVideoDurationProps {
  durations: IVideoDuration[];
  onDurationChange: (id: string, isChecked: boolean) => void;
}
const SidebarVideoDuration: React.FC<SidebarVideoDurationProps> = ({ durations, onDurationChange }) => {
  return (
    <div className="bd-course-widget widget-duration">
      <h5 className="bd-widget-title mb-20">Video Duration</h5>
      <div className="bd-widget-content">
        {durations.map((duration) => (
          <div className="checkbox-option" key={duration.id}>
            <input
              id={`course-duration-${duration.id}`}
              type="checkbox"
              onChange={(e) => onDurationChange(duration.id, e.target.checked)}
            />
            <label htmlFor={`course-duration-${duration.id}`}>
              {duration.label} <span>({duration.count})</span>
            </label>
          </div>
        ))}
      </div>
    </div>
  );
};

export default SidebarVideoDuration;
