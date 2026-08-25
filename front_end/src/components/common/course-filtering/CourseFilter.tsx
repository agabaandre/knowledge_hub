import React, { useState } from 'react';
import { courseCategoriesDataTwo } from '@/data/categories';
import { courseLevelsData } from '@/data/courses/LevelCheckbox';
import { coursePriceFilterData } from '@/data/courses/coursePriceFilterData';
import { courseRatingFilterData } from '@/data/courses/courseRatingFilterData';
import { instructorsData } from '@/data/courses/instructorFilterData';
import CategoryCheckbox from './CategoryCheckbox';
import LevelCheckbox from './LevelCheckbox';
import PriceFilterCheckbox from './PriceFilterCheckbox';
import RatingFilterCheckbox from './RatingFilterCheckbox';
import InstructorFilter from './InstructorFilter';
import LanguageFilter from './LanguageFilter';
import { Instructor } from '@/interFace/interFace';
import { languagesData } from '@/data/courses/languageData';
import SlideToggle from '@/utils/SlideToggle';
import useGlobalContext from '@/hooks/useContexts';

type Filters = {
  levels?: typeof courseLevelsData;
  priceFilters?: typeof coursePriceFilterData;
  ratingFilters?: typeof courseRatingFilterData;
  instructors?: Instructor[];
  categories?: typeof courseCategoriesDataTwo;
  languages?: string[];
};

type CourseFilterProps = {
  onFilterChange: (updatedFilters: Filters) => void;
  sliceLimits?: {
    instructors?: number;
    levels?: number;
    priceFilters?: number;
    categories?: number;
    languages?: number;
  };
};

const CourseFilter = ({ onFilterChange, sliceLimits = {} }: CourseFilterProps) => {
  const { isOpen } = useGlobalContext();

  // State management
  const [instructors, setInstructors] = useState<Instructor[]>(instructorsData);
  const [ratingFilters, setRatingFilters] = useState(courseRatingFilterData);
  const [priceFilters, setPriceFilters] = useState(coursePriceFilterData);
  const [levels, setLevels] = useState(courseLevelsData);
  const [categories] = useState(courseCategoriesDataTwo);
  const [languages] = useState(languagesData);

  // Apply slicing based on sliceLimits
  const slicedInstructors = instructors.slice(0, sliceLimits.instructors || instructors.length);
  const slicedLevels = levels.slice(0, sliceLimits.levels || levels.length);
  const slicedPriceFilters = priceFilters.slice(0, sliceLimits.priceFilters || priceFilters.length);
  const slicedCategories = categories.slice(0, sliceLimits.categories || categories.length);
  const slicedLanguages = languages.slice(0, sliceLimits.languages || languages.length);

  // Handlers for filter changes
  const handleLevelChange = (updatedLevels: typeof courseLevelsData) => {
    setLevels(updatedLevels);
    onFilterChange({ levels: updatedLevels });
  };

  const handlePriceFilterChange = (updatedFilters: typeof coursePriceFilterData) => {
    setPriceFilters(updatedFilters);
    onFilterChange({ priceFilters: updatedFilters });
  };

  const handleRatingFilterChange = (stars: number, isChecked: boolean) => {
    const updatedRatingFilters = ratingFilters.map((filter) =>
      filter.stars === stars ? { ...filter, isChecked } : filter
    );
    setRatingFilters(updatedRatingFilters);
    onFilterChange({ ratingFilters: updatedRatingFilters });
  };

  const handleInstructorChange = (updatedInstructors: Instructor[]) => {
    setInstructors(updatedInstructors);
    onFilterChange({ instructors: updatedInstructors });
  };

  return (
    <SlideToggle>
      <div className={`bd-course-filter-content mt-30 ${isOpen ? 'd-block' : 'd-none'}`}>
        <div className="container">
          <div className="row gy-30">
            <div className="bd-course-filter-widget">
              <div className="bd-course-filter-item widget-category">
                <h5 className="bd-widget-title mb-20">Categories</h5>
                <CategoryCheckbox categories={slicedCategories} />
              </div>
              <div className="bd-course-filter-item widget-level">
                <h5 className="bd-widget-title mb-20">Level</h5>
                <LevelCheckbox levels={slicedLevels} onChange={handleLevelChange} />
              </div>
              <div className="bd-course-filter-item widget-price">
                <h5 className="bd-widget-title mb-20">Price</h5>
                <PriceFilterCheckbox filters={slicedPriceFilters} onChange={handlePriceFilterChange} />
              </div>
              <div className="bd-course-filter-item widget-ratings">
                <h5 className="bd-widget-title mb-20">Ratings</h5>
                <RatingFilterCheckbox filters={ratingFilters} onChange={handleRatingFilterChange} />
              </div>
              <div className="bd-course-filter-item widget-instructors">
                <h5 className="bd-widget-title mb-20">Instructors</h5>
                <InstructorFilter instructors={slicedInstructors} onFilterChange={handleInstructorChange} />
              </div>
              <div className="bd-course-filter-item widget-language">
                <h5 className="bd-widget-title mb-20">Language</h5>
                <LanguageFilter languages={slicedLanguages} limit={sliceLimits.languages} />
              </div>
            </div>
          </div>
        </div>
      </div>
    </SlideToggle>
  );
};

export default CourseFilter;


