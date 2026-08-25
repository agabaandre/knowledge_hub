export interface Course {
    sl: number;
    code: string;
    title: string;
    theoryHours: number | null;
    theoryCredit: number | null;
    sessionalHours: number | null;
    sessionalCredit: number | null;
    totalCredit: number;
  }
  
  export interface Semester {
    title: string;
    courses: Course[];
    total: {
      theoryHours: number;
      theoryCredit: number;
      sessionalHours: number;
      sessionalCredit: number;
      totalCredit: number;
    };
  }
  
  export interface YearData {
    year: string;
    semesters: Semester[];
  }
  
  export type ICurriculumData = YearData[];
  