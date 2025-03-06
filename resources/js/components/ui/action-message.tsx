import * as React from "react"

import { cn } from "@/lib/utils"

interface ActionMessageProps extends React.ComponentProps<"div"> {}

function ActionMessage({ className, ...props }: ActionMessageProps) {
  return (
    <div
      data-slot="action-message"
      className={cn(
        "text-sm font-medium text-accent-foreground",
        className
      )}
      {...props}
    />
  )
}

export { ActionMessage }
