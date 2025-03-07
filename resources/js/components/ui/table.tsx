// @/components/ui/table.tsx
import * as React from "react";
import { cn } from "@/lib/utils";

interface TableProps extends React.ComponentProps<"table"> {
  data?: any[];
  emptyMessage?: string;
  children: React.ReactNode;
}

interface TableHeadProps extends React.ComponentProps<"thead"> {}
interface TableCellProps extends React.ComponentProps<"td"> {}
interface TableBodyProps extends React.ComponentProps<"tbody"> {}
interface TableRowProps extends React.ComponentProps<"tr"> {}

function Table({ className, data, emptyMessage = "Nothing to view on this table", children, ...props }: TableProps) {
  if (data && data.length === 0) {
    return (
      <div className="w-full p-4 text-center rounded-lg">
        {emptyMessage}
      </div>
    );
  }

  if (data === undefined && !children) {
    return (
      <div className="w-full p-4 text-center rounded-lg">
        Table not showing because there is no information or data to show.
      </div>
    );
  }

  const tableHead = React.Children.toArray(children).find(
    (child) => React.isValidElement(child) && child.type === TableHead
  ) as React.ReactElement<TableHeadProps> | undefined;

  let columnCount = 0;

  if (tableHead) {
    const firstRow = React.Children.toArray(tableHead.props.children).find(
      (child) => React.isValidElement(child) && child.type === "tr"
    ) as React.ReactElement;

    if (firstRow) {
      columnCount = React.Children.toArray(firstRow.props.children).filter(
        (child) => React.isValidElement(child) && child.type === TableCell
      ).length;
    }
  }

  const colGroup = columnCount
    ? (
      <colgroup>
        {Array.from({ length: columnCount }).map((_, index) => (
          <col key={index} className="w-auto" />
        ))}
      </colgroup>
    )
    : null;

  return (
    <div className={cn("w-full rounded-lg overflow-hidden", className)}>
      <table
        data-slot="table"
        className="w-full border-collapse table-auto border border-neutral-200 dark:border-neutral-800"
        {...props}
      >
        {colGroup}
        {children}
      </table>
    </div>
  );
}

function TableHead({ className, ...props }: TableHeadProps) {
  return (
    <thead
      data-slot="table-head"
      className={cn("font-bold", className)}
      {...props}
    >
      {props.children}
    </thead>
  );
}

function TableCell({ className, ...props }: TableCellProps) {
  return (
    <td
      data-slot="table-cell"
      className={cn("p-2 border-r border-neutral-200 dark:border-neutral-800", className)}
      {...props}
    />
  );
}

function TableBody({ className, ...props }: TableBodyProps) {
  return (
    <tbody
      data-slot="table-body"
      className={className}
      {...props}
    >
      {props.children}
    </tbody>
  );
}

function TableRow({ className, index, ...props }: TableRowProps & { index: number }) {
  const rowShade = index % 2 === 0 ? "bg-neutral-50 dark:bg-neutral-800" : "bg-white dark:bg-neutral-900";

  return (
    <tr
      data-slot="table-row"
      className={cn(`border-b border-neutral-200 dark:border-neutral-800 ${rowShade}`, className)}
      {...props}
    />
  );
}

export { Table, TableHead, TableCell, TableBody, TableRow };
