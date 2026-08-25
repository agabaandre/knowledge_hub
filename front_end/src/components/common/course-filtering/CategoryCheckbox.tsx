import { ICategoryFilter } from "@/interFace/interFace";
import React from "react";

export interface CategoryCheckboxProps {
  categories: ICategoryFilter[];
}

const CategoryCheckbox: React.FC<CategoryCheckboxProps> = ({ categories }) => {
  return (
    <div className="bd-widget-content">
      {categories.map((category, index) => (
        <div className="checkbox-option" key={index}>
          <input
            id={`course-check-${category.checkId}`}
            type="checkbox"
            defaultChecked={category.isChecked} 
          />
          <label htmlFor={`course-check-${category.checkId}`}>
            {category.name} <span>({category.count})</span>
          </label>
        </div>
      ))}
    </div>
  );
};

export default CategoryCheckbox;
