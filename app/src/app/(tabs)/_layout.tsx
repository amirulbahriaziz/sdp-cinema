import AppTabs from '@/components/app-tabs';

/**
 * Tab group layout — the app's bottom nav (Home / Explore). Nested inside the root Stack so
 * detail screens (movie/booking) push over the tabs. The NativeTabs triggers (`index`,
 * `explore`) resolve to the sibling route files in this group.
 */
export default function TabsLayout() {
  return <AppTabs />;
}
