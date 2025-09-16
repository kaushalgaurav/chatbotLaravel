

import { StartingNode, QuestionNode, ButtonsNode, YesNoNode, RatingNode, MessageNode, ConditionNode, FormulaNode, LogicNode} from "../landbot/components/index";



export const nodeTypes = {
  starting: StartingNode,
  question: QuestionNode,
  buttons: ButtonsNode,
  yesno: YesNoNode,
  rating: RatingNode,
  message: MessageNode,
  condition: ConditionNode,
  formula: FormulaNode,
  logic: LogicNode,
};

export const initialNodes = [
  {
    id: "1",
    type: "starting",
    position: { x: 100, y: 400 },
    data: {},
  },
];
