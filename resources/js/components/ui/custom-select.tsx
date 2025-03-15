import * as React from "react";
import { cn } from "@/lib/utils";

interface CustomSelectProps extends React.ComponentProps<"select"> {
  options: { value: string | number; label: string }[];
  placeholder?: string;
}

function CustomSelect({
  className,
  options,
  placeholder = "Select an option",
  ...props
}: CustomSelectProps) {
  return (
    <select
      data-slot="custom-select"
      className={cn(
        "border-input bg-background text-foreground w-full p-2 border rounded-md shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
        className
      )}
      {...props}
    >
      <option value="">{placeholder}</option>
      {options.map((option) => (
        <option key={option.value} value={option.value}>
          {option.label}
        </option>
      ))}
    </select>
  );
}

export { CustomSelect };
