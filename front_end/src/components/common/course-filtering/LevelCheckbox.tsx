import { ILevelFilter } from "@/interFace/interFace";
import React from "react";

export interface LevelCheckboxProps {
  levels: ILevelFilter[];
  onChange: (updatedLevels: ILevelFilter[]) => void;
}
const LevelCheckbox: React.FC<LevelCheckboxProps> = ({ levels, onChange }) => {

  // Handle checkbox state change
  const handleCheckboxChange = (index: number, isChecked: boolean) => {
    const updatedLevels = levels.map((level, i) =>
      i === index ? { ...level, isChecked } : level
    );
    onChange(updatedLevels);
  };

  return (
    <div className="bd-widget-content">
      {levels.map((level, index) => (
        <div className="checkbox-option" key={index}>
          <input
            id={`course-level-${level.checkId}`}
            type="checkbox"
            checked={level.isChecked}
            onChange={(e) => handleCheckboxChange(index, e.target.checked)}
          />
          <label htmlFor={`course-level-${level.checkId}`}>
            {level.name} <span>({level.count})</span>
          </label>
        </div>
      ))}
    </div>
  );
};

export default LevelCheckbox;
