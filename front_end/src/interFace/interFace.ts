import { StaticImageData } from "next/image";

// context api data type
export interface AppContextType {
  sideMenuOpen: boolean;
  toggleSideMenu: () => void;
  scrollDirection: string;
  setScrollDirection: React.Dispatch<React.SetStateAction<string>> | undefined;
  inputValue: string;
  setInputValue: React.Dispatch<React.SetStateAction<string>>;
  setSideMenuOpen: React.Dispatch<React.SetStateAction<boolean>>;
  filterType: string;
  setFilterType: React.Dispatch<React.SetStateAction<string>>;
  isVideoOpen: boolean;
  setIsVideoOpen: React.Dispatch<React.SetStateAction<boolean>>;
  openVideoModal: () => void;
  isOpen:boolean,
  toggleOpen:() => void;
  openSidebar:boolean,
  setOpenSidebar:React.Dispatch<React.SetStateAction<boolean>>;
  toggleSidebarMenu:() => void;
}
// Define the structure for each submenu
interface Submenu {
  title: string;
  link: string;
  previewImg?: StaticImageData;
  pluseIncon?: boolean;
  megaMenu?: Submenu[];
}

// Define the structure for each menu item
export interface MenuItem {
  id: number;
  hasDropdown: boolean;
  children: boolean;
  active: boolean;
  title: string;
  pluseIncon: boolean;
  link: string;
  columnFour?: boolean;
  previewImg?: boolean;
  submenus?: Submenu[];
  megaMenu?: boolean;
  pageLayout?: boolean;
  lastDropdown?: boolean;
}

// Define an interface for the categories data
export interface ICategories {
  id: number;
  title: string;
  totalCourses: number;
  icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
}
// Define an interface for the categories data
export interface ISchoolinCategories {
  id: number;
  categories: ICategories[];
}

// Define an interface for the testimonial data 
export interface ITestimonial {
  id: number,
  rating: number,
  content: string,
  name: string,
  avatar: StaticImageData,
  designation?: string;
  quoteImage?:StaticImageData;
  highlight?:string
}

// Define an interface for the instructor data 
export interface Iinstructor {
  id: number,
  name: string,
  title?: string,
  image: StaticImageData,
  role?: string,
  socialLinks?: {
    facebook: string,
    twitter: string,
    linkedin: string,
    instagram?: string,
  },
}
// Define an interface for the timeline data 
interface TimelineEvent {
  year: string;
  description: string;
}

export interface TimelineData {
  id: string;
  title: string;
  description: string;
  image: StaticImageData;
  events: TimelineEvent[];
}
// Define an interface for the mission vision data 
export interface MissionVisionImgData {
  image: StaticImageData;
}
export interface MissionVisionData {
  title: string;
  description: string;
}
// Define an interface for the counter data 
export interface ICounter {
  id: number,
  counterNum: number,
  suffix?: string;
  counterText: string;
  iconClass?:string
}

// Define an interface for the Feature data 
export interface IFeature {
  id: number,
  icon: string,
  title: string,
  description: string,
}
// Define an interface for the Course data 
export interface ICourse {
  id: number;
  badge?: string;
  badgeClass?: string;
  image: StaticImageData;
  imageClassName?: string;
  instructorImage?: StaticImageData;
  instructorImageClassName?: string;
  courseTextContent?: boolean;
  title: string;
  courseTitleClass?: string;
  FontSizeClass?: string;
  spacingClass?: string;
  smallText?: string;
  smallTextTwo?: string;
  courseTag?: string;
  lessons?: number;
  students?: number;
  courseName?: string;
  courseDescription: string;
  rating: number;
  price?: number;
  discount?: number;
  certificateBadge?: string;
  advancedTitle?: string;
  level?: string;
  details?: string;
  courseList?: string[];
  ratingNum?: number;
  courseTextContentStyle?: boolean;
  latterUppercase?: boolean;
  totalCourse?: number;
  instructorName?: string;
  hoursTime?: number;
  type?: string;
  brands?: StaticImageData[];
  buttonText?: string;
  courseTagTwo?: string;
  avatarImg?: StaticImageData
  category?: string[]
  shape?: StaticImageData;
  badgeClassTwo?: string;
  smallTextThree?: string;
  courseBgClass?: string;
  FontSizeClassTwo?: string;
  quantity?:number;
}
// Define an interface for the course props 
export interface ICourseProps {
  course: ICourse;
}

// Define an interface for the why choose data 
export interface WhyChooseDataType {
  id: number;
  icon: StaticImageData;
  title: string;
  description: string;
}
// Define an interface for the why choose data 
export interface CoreValue {
  icon: string;
  title: string;
  description: string;
}
// Define an interface for the why choose data 
export interface ISchoolingWhyChoose {
  id: number;
  icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
  title: string;
  description: string;
  wowDelayDuration: string
}
// Define an interface for the blog data 
export interface IBlog {
  id: number;
  image?: StaticImageData,
  title?: string,
  authorName?: string,
  date?: string,
  description?: string,
  month?: string;
  comments?: number;
  badge?: string;
  type?:string;
  quote?:string;
  images?:StaticImageData[];
  thumbnail?:StaticImageData;
  buttonShow?:boolean;
  buttonLink?:boolean;
  daynamicLink?:boolean
  boxShadowClass?:boolean

}
// Define an interface for the mission vision data 
export interface IMissionVision {
  id: number;
  img: StaticImageData;
  title: string;
  description: string;
}
// Define an interface for the blog data 
interface ContactDetail {
  text: string;
  link?: string;
}
export interface ContactItem {
  icon: string;
  title: string;
  details: (string | ContactDetail)[];
}
// Define an interface for the event data 
export interface IEvent {
  id: number,
  image?:StaticImageData;
  date: string,
  monthYear: string,
  location: string,
  time: string,
  title: string,
  description?:string;
  isActive?: boolean,
}
// Define an interface for the scholarship financial data 
export interface IScholarshipFinancialAid {
  image: StaticImageData;
  title: string;
  link: string;
}
// Define an interface for the review data 
export interface IReview {
  id: number;
  ratingIcon: string;
  contentTitle?: string;
  text: string;
  author: string;
}
// Define an interface for the excecutive leader data 
export interface IExecutiveLeadersType {
  id:number;
  image: StaticImageData;
  name: string;
  designation: string;
  instituteOne: string;
  instituteTwo: string;
  email: string;
  type: string;
}
// Define an interface for the book product data 
interface IBooks {
  id: number,
  image: StaticImageData,
  title: string,
  rating: number,
  price: number,
  quantity: number,
  discount: number,
}
export interface ProductsType {
  id: number,
  image?: StaticImageData,
  title: string,
  rating?: number,
  quantity?: number,
  badge?: string;
  books?: IBooks[]
  badgeClass?: string;
  description?:string;
  imageClassName?: string;
  instructorImage?: StaticImageData;
  instructorImageClassName?: string;
  courseTextContent?: boolean;
  courseTitleClass?: string;
  FontSizeClass?: string;
  spacingClass?: string;
  smallText?: string;
  smallTextTwo?: string;
  courseTag?: string;
  lessons?: number;
  students?: number;
  courseName?: string;
  price?: number;
  discount?: number;
  certificateBadge?: string;
  advancedTitle?: string;
  level?: string;
  details?: string;
  courseList?: string[];
  ratingNum?: number;
  courseTextContentStyle?: boolean;
  latterUppercase?: boolean;
  totalCourse?: number;
  instructorName?: string;
  hoursTime?: number;
  type?: string;
  brands?: StaticImageData[];
  buttonText?: string;
  courseTagTwo?: string;
  avatarImg?: StaticImageData
  category?: string[]
  shape?: StaticImageData;
  badgeClassTwo?: string;
  smallTextThree?: string;
  courseBgClass?: string;
  FontSizeClassTwo?: string;
  courseDescription?: string;
}
//Define an interface for the daynamic id type
export interface idType {
  id: number;
}
//Define an interface for the CTA type
export interface ICtaData {
  id: number;
  subtitle: string,
  title: string,
  buttonText: string,
  buttonLink: string,
  image: StaticImageData,
  bgClass: string,
}
//Define an interface for the Price Filtering type
export interface PriceFilter {
  name: string;
  count: number;
  isChecked: boolean;
  checkId:string;
}
//Define an interface for the instructor Filtering type
export interface Instructor {
  id: string;
  name: string;
  courseCount: number;
  isChecked: boolean;
}
//Define an interface for the course Rating filter
export interface IRatingFilter {
  stars: number;
  count: number;
  isChecked: boolean;
  checkId:string;
}
// Define the type for a single category
export interface ICategoryFilter {
  name: string;
  count: number;
  isChecked: boolean;
  checkId:string;
};
export interface IVideoDuration {
  id: string;
  label: string;
  count: number;
}
// Define the type for the subcategories
export interface ISubcategoryFilter {
  id: string;
  name: string;
  count: number;
}
// Define the type for the languages array
export interface ILanguageFilter {
  id: string;
  name: string;
  count: number;
}
// Define the type for the Feature array
export interface IFeatureFilter {
  id: string;
  name: string;
  count: number;
}
// Define the type for the Level array
export interface ILevelFilter {
  name: string;
  count: number;
  isChecked: boolean;
  checkId:string,
}
// Define the type for the instructor array
export interface IInstructorFilter {
  id: string;
  name: string,
  courseCount: number,
  isChecked: boolean
}
// Define the type for the course reviews array
export interface ICourseReview {
  id: number;
  name: string;
  date: string;
  avatar: StaticImageData;
  rating: number;
  comment: string;
  replies?: ICourseReview[];
  courseTitle?:string;
}
// Define the type for the program data array
export interface ProgramDataType {
  id: number;
  program?: string;
  type?: string;
  title: string;
  description: string;
  duration: string;
  credits?: string;
  image: StaticImageData;
  shapeImage: StaticImageData;
  textImage?: StaticImageData;
  BgClass?:string;
  hoursTime?:number;
  age?:string;
}
// Define the type for the academic data array
export interface IAcademicCalendar {
  semester: string;
  image: StaticImageData;
  events: IEventType[];
}
interface IEventType {
  label: string;
  date: string;
}



