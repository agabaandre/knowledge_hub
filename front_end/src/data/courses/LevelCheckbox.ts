import { ILevelFilter } from "@/interFace/interFace";

// data/courseLevelsData.ts
export const courseLevelsData:ILevelFilter[] = [
  { checkId: "1", name: "All Levels", count: 25, isChecked: false },
  { checkId: "2", name: "Beginner", count: 10, isChecked: true },
  { checkId: "3", name: "Mid Level", count: 7, isChecked: false },
  { checkId: "4", name: "Advanced Level", count: 8, isChecked: false },
];
//Course sidebar levels filter data
export const sidebarCourseLevelsFilterData:ILevelFilter[] = [
  { checkId: "5", name: "All Levels", count: 25, isChecked: false },
  { checkId: "6", name: "Beginner", count: 10, isChecked: true },
  { checkId: "7", name: "Mid Level", count: 7, isChecked: false },
  { checkId: "8", name: "Advanced Level", count: 8, isChecked: false },
];