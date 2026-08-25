import React, { useState, useEffect } from "react";
import { PriceFilter } from "@/interFace/interFace";

//Define an interface for the Price Filtering props type
 interface PriceFilterCheckboxProps {
  filters: PriceFilter[];
  onChange: (updatedFilters: PriceFilter[]) => void; // Add this line to define the onChange function
}
const PriceFilterCheckbox: React.FC<PriceFilterCheckboxProps> = ({ filters, onChange }) => {
  const [localFilters, setLocalFilters] = useState(filters);

  // Update localFilters when the filters prop changes
  useEffect(() => {
    setLocalFilters(filters);
  }, [filters]);

  // Handle checkbox state change
  const handleCheckboxChange = (index: number, isChecked: boolean) => {
    const updatedFilters = localFilters.map((filter, i) =>
      i === index ? { ...filter, isChecked } : filter
    );
    setLocalFilters(updatedFilters); // Update local state

    // Call the onChange prop to pass the updated filters to the parent
    onChange(updatedFilters);
  };

  return (
    <div className="bd-widget-content">
      {localFilters.map((filter, index) => (
        <div className="checkbox-option" key={index}>
          <input
            id={`course-price-${filter.checkId}`}
            type="checkbox"
            checked={filter.isChecked}
            onChange={(e) => handleCheckboxChange(index, e.target.checked)} // Handle checkbox change
          />
          <label htmlFor={`course-price-${filter.checkId}`}>
            {filter.name} <span>({filter.count})</span>
          </label>
        </div>
      ))}
    </div>
  );
};

export default PriceFilterCheckbox;
