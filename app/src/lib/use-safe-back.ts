import { useRouter, type Href } from 'expo-router';

/**
 * Pop the navigation stack if possible; otherwise navigate to a fallback route (Home by default).
 * Prevents "The action 'GO_BACK' was not handled by any navigator" on web reloads / deep-loads
 * where the history has no previous entry, and after replace-chains that shorten the stack.
 */
export function useSafeBack(fallback: Href = '/') {
  const router = useRouter();
  return () => (router.canGoBack() ? router.back() : router.replace(fallback));
}
