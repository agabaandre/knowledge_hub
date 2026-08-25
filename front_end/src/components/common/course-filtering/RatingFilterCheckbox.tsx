import React from 'react';

interface RatingFilterProps {
  filters: { checkId: string, stars: number; count: number; isChecked: boolean }[];
  onChange: (stars: number, isChecked: boolean) => void;
}

const RatingFilterCheckbox: React.FC<RatingFilterProps> = ({ filters, onChange }) => {
  return (
    <div className="bd-widget-content">
      {filters.map((filter) => (
        <div className="checkbox-option course-rating-spaceing" key={filter.stars}>
          <input
            id={`course-ratings-${filter.checkId}`}
            type="checkbox"
            checked={filter.isChecked}  
            onChange={(e) => onChange(filter.stars, e.target.checked)} 
          />
          <label htmlFor={`course-ratings-${filter.checkId}`}>
            {Array.from({ length: 5 }).map((_, i) => (
              <i
                key={i}
                className={`fa-sharp ${i < filter.stars ? 'fa-solid' : 'fa-regular'} fa-star`}
              ></i>
            ))}
            <span>({filter.count})</span>
          </label>
        </div>
      ))}
    </div>
  );
};

export default RatingFilterCheckbox;
