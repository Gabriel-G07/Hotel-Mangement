// @/components/ui/table.tsx
import * as React from "react"
import { cn } from "@/lib/utils"

interface TableProps extends React.ComponentProps<"table"> {}
interface TableHeadProps extends React.ComponentProps<"thead"> {}
interface TableCellProps extends React.ComponentProps<"td"> {}
interface TableBodyProps extends React.ComponentProps<"tbody"> {}
interface TableRowProps extends React.ComponentProps<"tr"> {}

function Table({ className, ...props }: TableProps) {
  return (
    <table
      data-slot="table"
      className={cn(
        "w-full border-separate border-spacing-0",
        className
      )}
      {...props}
    />
  )
}

function TableHead({ className, ...props }: TableHeadProps) {
  return (
    <thead
      data-slot="table-head"
      className={className}
      {...props}
    />
  )
}

function TableCell({ className, ...props }: TableCellProps) {
  return (
    <td
      data-slot="table-cell"
      className={className}
      {...props}
    />
  )
}

function TableBody({ className, ...props }: TableBodyProps) {
  return (
    <tbody
      data-slot="table-body"
      className={className}
      {...props}
    />
  )
}

function TableRow({ className, ...props }: TableRowProps) {
  return (
    <tr
      data-slot="table-row"
      className={className}
      {...props}
    />
  )
}

export { Table, TableHead, TableCell, TableBody, TableRow }
