/**
 * Ambient declarations for CSS imports used by the web target (global.css + CSS modules).
 * Native ignores these at runtime; this keeps `tsc --noEmit` green across the workspace.
 */
declare module '*.css';

declare module '*.module.css' {
  const classes: { readonly [key: string]: string };
  export default classes;
}
