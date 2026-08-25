import { Instructor } from "@/interFace/interFace";
import React from "react";

type InstructorFilterProps = {
  instructors: Instructor[];
  onFilterChange: (updatedInstructors: Instructor[]) => void;
};

const InstructorFilter: React.FC<InstructorFilterProps> = ({
  instructors,
  onFilterChange,
}) => {
  const handleCheckboxChange = (id: string, isChecked: boolean) => {
    const updatedInstructors = instructors.map((instructor) =>
      instructor.id === id ? { ...instructor, isChecked } : instructor
    );
    onFilterChange(updatedInstructors);
  };

  return (
    <div className="bd-widget-content">
      {instructors.map((instructor) => (
        <div className="checkbox-option" key={instructor.id}>
          <input
            id={`course-instructor-${instructor.id}`}
            type="checkbox"
            checked={instructor.isChecked}
            onChange={(e) =>
              handleCheckboxChange(instructor.id, e.target.checked)
            }
          />
          <label htmlFor={`course-instructor-${instructor.id}`}>
            {instructor.name} <span>({instructor.courseCount})</span>
          </label>
        </div>
      ))}
    </div>
  );
};

export default InstructorFilter;
