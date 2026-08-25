import blogData from "@/data/blog-data";
import coursesData from "@/data/courses/courses-data";
import { eventData } from "@/data/events-data";
import { instructorsData } from "@/data/instructor-data";
import productsData from "@/data/products-data";
import programData from "@/data/programe-data";

function uniqueIds(items: { id: number }[]): string[] {
  return [...new Set(items.map((item) => String(item.id)))];
}

export function courseStaticParams(): { courseId: string }[] {
  return uniqueIds(coursesData).map((courseId) => ({ courseId }));
}

export function blogStaticParams(): { blogId: string }[] {
  return uniqueIds(blogData).map((blogId) => ({ blogId }));
}

export function productStaticParams(): { id: string }[] {
  return uniqueIds(productsData).map((id) => ({ id }));
}

export function instructorStaticParams(): { id: string }[] {
  return uniqueIds(instructorsData).map((id) => ({ id }));
}

export function eventStaticParams(): { id: string }[] {
  return uniqueIds(eventData).map((id) => ({ id }));
}

export function kindergartenProgramStaticParams(): { id: string }[] {
  return uniqueIds(programData).map((id) => ({ id }));
}

export function programStaticParams(): { programId: string }[] {
  return uniqueIds(programData).map((programId) => ({ programId }));
}
