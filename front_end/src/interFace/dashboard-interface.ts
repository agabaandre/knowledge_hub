
// Interface representing the structure of earning data
export interface EarningData {
    icon: string;
    amount: number;
    label: string;
    suffix: string;
}

// Define the type for assignment data
export interface IInstructorAssignment {
    id: string;
    user: string;
    quiz: string;
    result: string;
    time: string;
    status: string;
    statusClass: string;
}
// Define the type for AnnouncementItemProps data
export interface IAnnouncement {
    title: string;
    date: string;
    time: string;
    description: string;
}
// Define the type for QuizAttempt data
export interface QuizAttempt {
    id: string;
    user: string;
    quiz: string;
    time: string;
    status: 'Complete' | 'Incomplete' | 'Pending' | 'Done';
}

// Define types for the purchase data
export interface IPurchaseItem {
    course: string;
    price: string;
    paymentStatus: string;
    paymentMethod: string;
    date: string;
  }
// Define types for the reviews data
  export interface IReviewItem {
    title: string;
    rating: number;
    reviewsCount: number;
    description: string;
  }
  // Define types for the student dashboard Assignment data
  export type IStudentAssignment = {
    id: string;
    user: string;
    quiz: string;
    result: string;
    time: string;
    status: 'Pass' | 'Fail';
};
// Student progrss data type
export type ICounterItem = {
  icon: string;
  count: number;
  text: string;
  symbol?: string; // Optional
};
